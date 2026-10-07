<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            // Barbería
            [
                'name' => 'Corte Clásico',
                'description' => 'Corte tradicional a tijera y máquina con acabado pulido y perfilado de cuello.',
                'price' => 250.00,
                'duration_minutes' => 45,
                'category' => 'barberia',
                'image_path' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Corte Fade / Degradado',
                'description' => 'Degradado limpio a navaja o shaver con texturizado superior moderno.',
                'price' => 280.00,
                'duration_minutes' => 50,
                'category' => 'barberia',
                'image_path' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Afeitado Toalla Caliente',
                'description' => 'Ritual tradicional de afeitado con toallas aromatizadas y bálsamo hidratante.',
                'price' => 220.00,
                'duration_minutes' => 40,
                'category' => 'barberia',
                'image_path' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Perfilado y Arreglo de Barba',
                'description' => 'Diseño y alineación precisa de contornos de barba con navaja y aceites esenciales.',
                'price' => 180.00,
                'duration_minutes' => 30,
                'category' => 'barberia',
                'image_path' => null,
                'is_active' => true,
            ],
            // Estética
            [
                'name' => 'Corte y Estilizado Dama',
                'description' => 'Corte de diseño personalizado con lavado relajante, secado y moldeado profesional.',
                'price' => 350.00,
                'duration_minutes' => 60,
                'category' => 'estetica',
                'image_path' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Tinte y Colorimetría',
                'description' => 'Aplicación de color uniforme o retoque de raíz con tratamiento protector del brillo.',
                'price' => 650.00,
                'duration_minutes' => 90,
                'category' => 'estetica',
                'image_path' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Tratamiento Capilar Hidratante',
                'description' => 'Mascarilla profunda reconstructora para sellar puntas, devolver brillo y suavidad.',
                'price' => 400.00,
                'duration_minutes' => 45,
                'category' => 'estetica',
                'image_path' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Lavado y Peinado Express',
                'description' => 'Lavado capilar con masaje relajante y peinado rápido con secadora y plancha.',
                'price' => 200.00,
                'duration_minutes' => 30,
                'category' => 'estetica',
                'image_path' => null,
                'is_active' => true,
            ],
        ];

        foreach ($services as $data) {
            Service::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}
