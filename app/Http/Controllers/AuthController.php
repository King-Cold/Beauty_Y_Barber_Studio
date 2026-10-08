<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            // Lógica preparada para determinar el destino según el rol del usuario
            // 1: Administrador, 2: Recepcionista, 3: Trabajador, 4: Cliente
            $destination = '/';
            
            switch ($user->role_id) {
                case 1:
                    $destination = '/admin'; // Dashboard de Administrador
                    break;
                case 2:
                    $destination = '/recepcion'; // Dashboard de Recepcionista
                    break;
                case 3:
                    $destination = '/trabajador'; // Dashboard de Trabajador
                    break;
                case 4:
                    $destination = '/cliente'; // Perfil de Cliente
                    break;
            }

            // Implementamos la redirección usando $destination según el rol
            return redirect()->intended($destination)->with('success', '¡Inicio de sesión exitoso! Bienvenido de nuevo.');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'apellidos' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'telefono' => ['required', 'string', 'max:20', 'unique:users,telefono'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:12', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[\x20-\x7E]+$/'],
            'fecha_nacimiento' => ['nullable', 'date', 'after:1920-01-01', 'before:-5 years'],
            'terms' => ['accepted'],
            'privacy' => ['accepted'],
        ], [
            'terms.accepted' => 'Debes aceptar los Términos y Condiciones.',
            'privacy.accepted' => 'Debes aceptar el Aviso de Privacidad.',
            'telefono.unique' => 'El número de teléfono ya está registrado.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.regex' => 'La contraseña no es válida. Debe incluir una mayúscula, una minúscula, un número, un carácter especial y no contener emojis ni caracteres de otros idiomas.',
            'fecha_nacimiento.before' => 'Debes tener al menos 5 años para poder registrarte.',
            'fecha_nacimiento.after' => 'Por favor ingresa un año de nacimiento válido.',
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'apellidos.regex' => 'Los apellidos solo pueden contener letras y espacios.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'apellidos' => $validated['apellidos'],
            'telefono' => $validated['telefono'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
            'role_id' => 4, // Rol por defecto: Cliente
        ]);

        Auth::login($user);

        return redirect('/cliente')->with('success', '¡Cuenta creada e inicio de sesión exitoso!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Has cerrado sesión exitosamente.');
    }
}
