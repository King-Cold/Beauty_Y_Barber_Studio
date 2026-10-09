<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = \App\Models\Service::with(['trabajadores' => function ($q) {
            $q->orderBy('nombre');
        }])->orderBy('is_active', 'desc')->get();
        return view('admin.services', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:services,name',
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
                'not_regex:/^[0-9\s]+$/u',
            ],
            'description' => 'required|string',
            'price' => 'required|numeric|gt:0',
            'duration_minutes' => 'required|integer|min:20',
            'category' => 'required|in:barberia,estetica',
            'image' => 'required|image|max:2048',
        ], [
            'name.required' => 'El nombre del servicio es obligatorio.',
            'name.unique' => 'Ya existe un servicio registrado con este nombre.',
            'name.regex' => 'El nombre del servicio solo puede contener caracteres alfanuméricos (letras y números). No se permiten signos ni caracteres especiales.',
            'name.not_regex' => 'El nombre del servicio no puede estar compuesto exclusivamente por números.',
            'price.required' => 'El precio del servicio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número válido.',
            'price.gt' => 'El precio debe ser mayor a cero (no se permite 0 ni valores negativos).',
            'duration_minutes.min' => 'La duración mínima permitida es de 20 minutos.',
            'duration_minutes.required' => 'La duración del servicio es obligatoria.',
            'duration_minutes.integer' => 'La duración debe ser un número entero de minutos.',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('services', 'public');
            $validated['image_path'] = '/storage/' . $path;
        }

        $validated['is_active'] = true;

        \App\Models\Service::create($validated);

        return redirect()->route('admin.services')->with('success', 'Servicio registrado correctamente.');
    }

    public function update(Request $request, \App\Models\Service $service)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:services,name,' . $service->id,
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
                'not_regex:/^[0-9\s]+$/u',
            ],
            'description' => 'required|string',
            'price' => 'required|numeric|gt:0',
            'duration_minutes' => 'required|integer|min:20',
            'category' => 'required|in:barberia,estetica',
            'image' => 'nullable|image|max:2048',
        ], [
            'name.required' => 'El nombre del servicio es obligatorio.',
            'name.unique' => 'Ya existe un servicio registrado con este nombre.',
            'name.regex' => 'El nombre del servicio solo puede contener caracteres alfanuméricos (letras y números). No se permiten signos ni caracteres especiales.',
            'name.not_regex' => 'El nombre del servicio no puede estar compuesto exclusivamente por números.',
            'price.required' => 'El precio del servicio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número válido.',
            'price.gt' => 'El precio debe ser mayor a cero (no se permite 0 ni valores negativos).',
            'duration_minutes.min' => 'La duración mínima permitida es de 20 minutos.',
            'duration_minutes.required' => 'La duración del servicio es obligatoria.',
            'duration_minutes.integer' => 'La duración debe ser un número entero de minutos.',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('services', 'public');
            $validated['image_path'] = '/storage/' . $path;
        }

        $service->update($validated);

        return redirect()->route('admin.services')->with('success', 'Servicio actualizado correctamente.');
    }

    public function toggleStatus(\App\Models\Service $service)
    {
        $service->is_active = !$service->is_active;
        $service->save();

        $statusName = $service->is_active ? 'activado' : 'desactivado';
        return redirect()->route('admin.services')->with('success', "Servicio $statusName correctamente.");
    }

    public function destroy(\App\Models\Service $service)
    {
        if ($service->image_path) {
            $relativePath = str_replace('/storage/', '', $service->image_path);
            \Illuminate\Support\Facades\Storage::disk('public')->delete($relativePath);
        }

        $serviceName = $service->name;
        $service->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "El servicio \"{$serviceName}\" ha sido eliminado exitosamente."
            ]);
        }

        return redirect()->route('admin.services')->with('success', "Servicio \"{$serviceName}\" eliminado correctamente.");
    }
}
