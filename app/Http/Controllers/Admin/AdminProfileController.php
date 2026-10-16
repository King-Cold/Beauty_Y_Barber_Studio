<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Service;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminProfileController extends Controller
{
    /**
     * Muestra la vista del perfil del Administrador con métricas del sistema.
     */
    public function perfil()
    {
        $user = Auth::user();

        if (!$user || $user->role_id !== 1) {
            abort(403, 'Acceso denegado al perfil de administrador.');
        }

        // Estadísticas del sistema para el banner del Administrador
        $totalUsers = User::count();
        $totalServices = Service::count();
        $totalCitas = Cita::count();

        return view('admin.perfil', compact('user', 'totalUsers', 'totalServices', 'totalCitas'));
    }

    /**
     * Actualiza los datos personales del Administrador.
     */
    public function actualizarDatos(Request $request, ProfileService $profileService)
    {
        $user = Auth::user();

        if (!$user || $user->role_id !== 1) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Acceso denegado.'], 403);
            }
            abort(403, 'Acceso denegado.');
        }

        try {
            $profileService->updateProfile($user, $request->all());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Datos de perfil de administrador actualizados correctamente.',
                    'user' => [
                        'name' => $user->name,
                        'apellidos' => $user->apellidos,
                        'email' => $user->email,
                        'telefono' => $user->telefono,
                        'fecha_nacimiento' => $user->fecha_nacimiento,
                    ],
                ]);
            }

            return back()->with('success', 'Datos de perfil de administrador actualizados correctamente.');
        } catch (ValidationException $e) {
            $primerError = collect($e->errors())->flatten()->first() ?? 'Por favor verifica la información ingresada.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $primerError,
                    'errors' => $e->errors(),
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error inesperado al actualizar el perfil: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al actualizar los datos.');
        }
    }

    /**
     * Actualiza la contraseña del Administrador verificando la actual y las reglas de seguridad.
     */
    public function actualizarPassword(Request $request, ProfileService $profileService)
    {
        $user = Auth::user();

        if (!$user || $user->role_id !== 1) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Acceso denegado.'], 403);
            }
            abort(403, 'Acceso denegado.');
        }

        try {
            $resultado = $profileService->changePassword($user, $request->all());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $resultado['message'] ?? 'Contraseña de administrador actualizada con éxito.',
                ]);
            }

            return back()->with('success', $resultado['message'] ?? 'Contraseña de administrador actualizada con éxito.');
        } catch (ValidationException $e) {
            $primerError = collect($e->errors())->flatten()->first() ?? 'Error al actualizar la contraseña.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $primerError,
                    'errors' => $e->errors(),
                ], 422);
            }

            return back()->withErrors($e->errors());
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la contraseña: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al actualizar la contraseña.');
        }
    }
}
