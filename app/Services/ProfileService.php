<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileService
{
    /**
     * Reglas de validación para el cambio de contraseña.
     *
     * @param bool $isConfirmed
     * @return array
     */
    public function getPasswordValidationRules(bool $isConfirmed = true): array
    {
        $rules = [
            'required',
            'string',
            'min:8',
            // Alfanumérica: requiere al menos una letra y al menos un número
            'regex:/^(?=.*[a-zA-Z])(?=.*\d)/',
        ];

        if ($isConfirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }

    /**
     * Mensajes descriptivos en español para las validaciones de contraseña.
     *
     * @return array
     */
    public function getPasswordValidationMessages(): array
    {
        return [
            'current_password.required' => 'Debes ingresar tu contraseña actual.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La nueva contraseña debe ser alfanumérica (contener letras y al menos un número).',
            'password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
            'new_password.required' => 'La nueva contraseña es obligatoria.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.regex' => 'La nueva contraseña debe ser alfanumérica (contener letras y al menos un número).',
            'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
            'new_password.same' => 'La confirmación de la nueva contraseña no coincide.',
        ];
    }

    /**
     * Normaliza los datos de entrada para cambio de contraseña soportando diferentes nombres de campos.
     *
     * @param array $data
     * @return array
     */
    public function normalizePasswordData(array $data): array
    {
        $currentPassword = $data['current_password'] ?? $data['currentPass'] ?? null;
        $password = $data['password'] ?? $data['new_password'] ?? $data['newPass'] ?? null;
        $passwordConfirmation = $data['password_confirmation'] ?? $data['new_password_confirmation'] ?? $data['confirmPass'] ?? $data['confirm_password'] ?? null;

        return [
            'current_password' => $currentPassword,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ];
    }

    /**
     * Valida el formulario de cambio de contraseña verificando:
     * 1. Presencia de contraseña actual y nueva.
     * 2. Concordancia criptográfica de la contraseña actual con Hash::check.
     * 3. Reglas de la nueva contraseña (alfanumérica, min 8, confirmada).
     * 4. Que la nueva contraseña no sea idéntica a la contraseña actual.
     *
     * @param User $user
     * @param array $data
     * @throws ValidationException
     * @return array Datos normalizados y validados
     */
    public function validatePasswordChange(User $user, array $data): array
    {
        $normalized = $this->normalizePasswordData($data);

        $validator = Validator::make($normalized, [
            'current_password' => ['required', 'string'],
            'password' => $this->getPasswordValidationRules(isConfirmed: true),
        ], $this->getPasswordValidationMessages());

        // Hook para validación de lógica de negocio y criptográfica
        $validator->after(function ($v) use ($user, $normalized) {
            $currentPassword = $normalized['current_password'];
            $newPassword = $normalized['password'];

            // 1. Validar que la contraseña actual sea la correcta
            if (!empty($currentPassword)) {
                if (!Hash::check($currentPassword, $user->password)) {
                    $v->errors()->add('current_password', 'La contraseña actual ingresada es incorrecta.');
                }
            }

            // 2. Validar que la nueva contraseña no sea igual a la actual
            if (!empty($currentPassword) && !empty($newPassword)) {
                if (Hash::check($newPassword, $user->password)) {
                    $v->errors()->add('password', 'La nueva contraseña debe ser diferente a tu contraseña actual.');
                }
            }
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $normalized;
    }

    /**
     * Aplica la actualización segura de contraseña en la base de datos con Hash::make.
     *
     * @param User $user
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword(User $user, string $newPassword): bool
    {
        $user->password = Hash::make($newPassword);
        return $user->save();
    }

    /**
     * Orquestador completo para validar y cambiar la contraseña de un usuario.
     *
     * @param User $user
     * @param array $data
     * @throws ValidationException
     * @return array
     */
    public function changePassword(User $user, array $data): array
    {
        $validated = $this->validatePasswordChange($user, $data);
        $this->updatePassword($user, $validated['password']);

        return [
            'success' => true,
            'message' => 'Tu contraseña ha sido actualizada con éxito.',
        ];
    }

    /**
     * Obtiene las reglas de validación para los datos personales de perfil,
     * garantizando que el email y teléfono ignoren el ID del usuario actual.
     *
     * @param User $user
     * @param array $extraRules Reglas adicionales según rol (ej. dirección para trabajador)
     * @return array
     */
    public function getProfileValidationRules(User $user, array $extraRules = []): array
    {
        $baseRules = [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'apellidos' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'telefono')->ignore($user->id),
            ],
            'fecha_nacimiento' => ['nullable', 'date', 'after:1920-01-01', 'before_or_equal:today'],
        ];

        return array_merge($baseRules, $extraRules);
    }

    /**
     * Mensajes descriptivos en español para validación de datos de perfil.
     *
     * @param array $extraMessages
     * @return array
     */
    public function getProfileValidationMessages(array $extraMessages = []): array
    {
        $baseMessages = [
            'name.required' => 'El nombre es obligatorio.',
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.regex' => 'Los apellidos solo pueden contener letras y espacios.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'El correo electrónico ya está registrado por otro usuario.',
            'telefono.unique' => 'El número telefónico ya está registrado por otro usuario.',
            'telefono.max' => 'El número telefónico no puede exceder los 20 caracteres.',
            'fecha_nacimiento.date' => 'La fecha de nacimiento no tiene un formato válido.',
            'fecha_nacimiento.after' => 'Por favor ingresa una fecha de nacimiento válida posterior a 1920.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento no puede ser una fecha futura ni el día de mañana.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser una fecha futura ni el día de mañana.',
        ];

        return array_merge($baseMessages, $extraMessages);
    }

    /**
     * Valida los datos del perfil de usuario aplicando reglas de unicidad e integridad.
     *
     * @param User $user
     * @param array $data
     * @param array $extraRules
     * @param array $extraMessages
     * @throws ValidationException
     * @return array
     */
    public function validateProfileData(User $user, array $data, array $extraRules = [], array $extraMessages = []): array
    {
        // Si el correo no viene en los datos (por ejemplo, campo de solo lectura en frontend), conservar el actual
        if (empty($data['email']) && !empty($user->email)) {
            $data['email'] = $user->email;
        }

        $rules = $this->getProfileValidationRules($user, $extraRules);
        $messages = $this->getProfileValidationMessages($extraMessages);

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Actualiza los datos de perfil de un usuario y opcionalmente datos adicionales.
     *
     * @param User $user
     * @param array $data
     * @param array $extraRules
     * @param array $extraMessages
     * @throws ValidationException
     * @return User
     */
    public function updateProfile(User $user, array $data, array $extraRules = [], array $extraMessages = []): User
    {
        $validated = $this->validateProfileData($user, $data, $extraRules, $extraMessages);

        // Actualizar campos base de usuario
        $user->name = $validated['name'];
        $user->apellidos = $validated['apellidos'];
        $user->email = $validated['email'];

        if (array_key_exists('telefono', $validated)) {
            $user->telefono = $validated['telefono'];
        }

        if (array_key_exists('fecha_nacimiento', $validated)) {
            $user->fecha_nacimiento = $validated['fecha_nacimiento'];
        }

        $user->save();

        return $user;
    }
}
