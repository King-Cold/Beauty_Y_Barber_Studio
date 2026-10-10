<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Muestra la lista de usuarios del sistema obtenidos de la base de datos.
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('id')->get();

        $query = User::with(['role', 'trabajador'])->orderBy('id', 'asc');

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        $users = $query->get();

        $stats = [
            'total' => User::count(),
            'admins' => User::where('role_id', 1)->count(),
            'recepcionistas' => User::where('role_id', 2)->count(),
            'trabajadores' => User::where('role_id', 3)->count(),
            'clientes' => User::where('role_id', 4)->count(),
        ];

        return view('admin.usuarios', compact('users', 'roles', 'stats'));
    }

    /**
     * Almacena un nuevo usuario en la base de datos (Admin, Recepcionista, Cliente).
     */
    public function store(Request $request)
    {
        // Normalizar entradas
        if ($request->has('name')) {
            $request->merge(['name' => trim(preg_replace('/\s+/', ' ', $request->name))]);
        }
        if ($request->has('apellidos')) {
            $request->merge(['apellidos' => trim(preg_replace('/\s+/', ' ', $request->apellidos))]);
        }
        if ($request->has('email')) {
            $request->merge(['email' => strtolower(trim($request->email))]);
        }
        if ($request->has('telefono')) {
            $request->merge(['telefono' => trim($request->telefono)]);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            ],
            'apellidos' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            ],
            'telefono' => [
                'required',
                'string',
                'size:10',
                'regex:/^[0-9]{10}$/',
                'unique:users,telefono',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[\W_]/',
            ],
            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.regex' => 'El campo Nombre únicamente debe contener letras del abecedario.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.regex' => 'El campo Apellidos únicamente debe contener letras del abecedario.',
            'telefono.required' => 'El teléfono de contacto es obligatorio.',
            'telefono.size' => 'El número de teléfono debe contener exactamente 10 dígitos numéricos.',
            'telefono.regex' => 'El número de teléfono debe contener únicamente 10 dígitos numéricos.',
            'telefono.unique' => 'Ya existe un usuario registrado con este número de teléfono.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Introduce un correo electrónico válido.',
            'email.regex' => 'Introduce un correo electrónico válido (ej. usuario@dominio.com).',
            'email.unique' => 'Ya existe un usuario registrado con este correo electrónico.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, un número y un carácter especial.',
            'role_id.required' => 'Debes seleccionar un rol para el usuario.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
        ]);

        // Si se intenta registrar rol 3 (Trabajador) directamente por este endpoint, rechazar
        // ya que debe crearse con su información laboral en TrabajadorController
        if ((int)$validated['role_id'] === 3) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las cuentas de Barberos y Estilistas deben registrarse completando la información laboral en el módulo de Trabajadores.',
                    'redirect' => route('admin.trabajadores.index') . '?from_usuarios=1'
                ], 422);
            }
            return redirect()->route('admin.trabajadores.index')
                ->with('warning', 'Las cuentas de Barberos y Estilistas deben registrarse en el módulo de Trabajadores.');
        }

        $user = \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            return User::create([
                'name' => $validated['name'],
                'apellidos' => $validated['apellidos'],
                'telefono' => $validated['telefono'],
                'email' => $validated['email'],
                'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                'role_id' => $validated['role_id'],
            ]);
        });

        $user->load('role');

        $stats = [
            'total' => User::count(),
            'admins' => User::where('role_id', 1)->count(),
            'recepcionistas' => User::where('role_id', 2)->count(),
            'trabajadores' => User::where('role_id', 3)->count(),
            'clientes' => User::where('role_id', 4)->count(),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Usuario "' . $user->name . ' ' . $user->apellidos . '" registrado exitosamente.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'apellidos' => $user->apellidos,
                    'nombre_completo' => $user->name . ' ' . $user->apellidos,
                    'email' => $user->email,
                    'telefono' => $user->telefono,
                    'role_id' => $user->role_id,
                    'role_nombre' => $user->role->nombre ?? 'Usuario',
                    'role_badge_class' => match($user->role_id) {
                        1 => 'badge-role-admin',
                        2 => 'badge-role-recepcion',
                        3 => 'badge-role-trabajador',
                        4 => 'badge-role-cliente',
                        default => 'badge-role-cliente'
                    },
                    'activo' => true,
                    'created_at' => $user->created_at ? $user->created_at->format('d/m/Y') : date('d/m/Y'),
                    'initials' => strtoupper(substr($user->name, 0, 1) . substr($user->apellidos, 0, 1)),
                ],
                'stats' => $stats
            ]);
        }

        return redirect()->route('admin.usuarios')->with('success', 'Usuario registrado exitosamente.');
    }

    /**
     * Actualiza la información de una cuenta de usuario existente.
     */
    public function update(Request $request, User $user)
    {
        // Normalizar entradas
        if ($request->has('name')) {
            $request->merge(['name' => trim(preg_replace('/\s+/', ' ', $request->name))]);
        }
        if ($request->has('apellidos')) {
            $request->merge(['apellidos' => trim(preg_replace('/\s+/', ' ', $request->apellidos))]);
        }
        if ($request->has('email')) {
            $request->merge(['email' => strtolower(trim($request->email))]);
        }
        if ($request->has('telefono')) {
            $request->merge(['telefono' => trim($request->telefono)]);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            ],
            'apellidos' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            ],
            'telefono' => [
                'required',
                'string',
                'size:10',
                'regex:/^[0-9]{10}$/',
                \Illuminate\Validation\Rule::unique('users', 'telefono')->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[\W_]/',
            ],
            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.regex' => 'El campo Nombre únicamente debe contener letras del abecedario.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.regex' => 'El campo Apellidos únicamente debe contener letras del abecedario.',
            'telefono.required' => 'El teléfono de contacto es obligatorio.',
            'telefono.size' => 'El número de teléfono debe contener exactamente 10 dígitos numéricos.',
            'telefono.regex' => 'El número de teléfono debe contener únicamente 10 dígitos numéricos.',
            'telefono.unique' => 'Ya existe un usuario registrado con este número de teléfono.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Introduce un correo electrónico válido.',
            'email.regex' => 'Introduce un correo electrónico válido (ej. usuario@dominio.com).',
            'email.unique' => 'Ya existe un usuario registrado con este correo electrónico.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, un número y un carácter especial.',
            'role_id.required' => 'Debes seleccionar un rol para el usuario.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
        ]);

        $newRoleId = (int)$validated['role_id'];
        $hasWorkerProfile = $user->trabajador()->exists();

        // Si se cambia el rol a Trabajador (3) y la cuenta aún no tiene perfil en trabajadores
        if ($newRoleId === 3 && !$hasWorkerProfile) {
            $user->update([
                'name' => $validated['name'],
                'apellidos' => $validated['apellidos'],
                'telefono' => $validated['telefono'],
                'email' => $validated['email'],
                'role_id' => 3,
            ]);
            if (!empty($validated['password'])) {
                $user->update(['password' => \Illuminate\Support\Facades\Hash::make($validated['password'])]);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('admin.trabajadores.index') . '?from_usuarios=1',
                    'message' => 'Rol actualizado a Barbero/Estilista. Redirigiendo para completar expediente laboral...',
                    'user' => [
                        'id' => $user->id,
                        'nombre' => $user->name,
                        'apellidos' => $user->apellidos,
                        'email' => $user->email,
                        'telefono' => $user->telefono,
                    ]
                ]);
            }

            return redirect()->route('admin.trabajadores.index')
                ->with('info', 'Por favor completa la información laboral del nuevo trabajador.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $validated) {
            $updateData = [
                'name' => $validated['name'],
                'apellidos' => $validated['apellidos'],
                'telefono' => $validated['telefono'],
                'email' => $validated['email'],
                'role_id' => $validated['role_id'],
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
            }

            $user->update($updateData);
        });

        $user->load(['role', 'trabajador']);

        $stats = [
            'total' => User::count(),
            'admins' => User::where('role_id', 1)->count(),
            'recepcionistas' => User::where('role_id', 2)->count(),
            'trabajadores' => User::where('role_id', 3)->count(),
            'clientes' => User::where('role_id', 4)->count(),
        ];

        $isActive = $user->trabajador ? (bool)$user->trabajador->activo : true;

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cuenta de usuario "' . $user->name . ' ' . $user->apellidos . '" actualizada exitosamente.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'apellidos' => $user->apellidos,
                    'nombre_completo' => $user->name . ' ' . $user->apellidos,
                    'email' => $user->email,
                    'telefono' => $user->telefono,
                    'role_id' => $user->role_id,
                    'role_nombre' => $user->role->nombre ?? 'Usuario',
                    'has_worker' => $user->trabajador ? 1 : 0,
                    'activo' => $isActive,
                    'status_str' => $isActive ? 'Activo' : 'Inactivo',
                    'created_at' => $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : 'N/A',
                    'initials' => strtoupper(substr($user->name, 0, 1) . substr($user->apellidos, 0, 1)),
                ],
                'stats' => $stats
            ]);
        }

        return redirect()->route('admin.usuarios')->with('success', 'Cuenta de usuario actualizada exitosamente.');
    }

    /**
     * Elimina un usuario del sistema.
     */
    public function destroy(Request $request, User $user)
    {
        if (auth()->check() && auth()->id() === $user->id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propia cuenta de usuario en sesión actual.'
                ], 422);
            }
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
            if ($user->trabajador) {
                $user->trabajador->delete();
            }
            $user->delete();
        });

        $stats = [
            'total' => User::count(),
            'admins' => User::where('role_id', 1)->count(),
            'recepcionistas' => User::where('role_id', 2)->count(),
            'trabajadores' => User::where('role_id', 3)->count(),
            'clientes' => User::where('role_id', 4)->count(),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado correctamente.',
                'stats' => $stats
            ]);
        }

        return redirect()->route('admin.usuarios')->with('success', 'Usuario eliminado correctamente.');
    }
}
