<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = \App\Models\Service::orderBy('is_active', 'desc')->get();
        return view('admin.services', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'category' => 'required|in:barberia,estetica',
            'image' => 'required|image|max:2048',
        ], [
            'name.unique' => 'Ya existe un servicio registrado con este nombre.',
            'price.min' => 'El precio no puede ser un valor negativo.',
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
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'category' => 'required|in:barberia,estetica',
            'image' => 'nullable|image|max:2048',
        ], [
            'name.unique' => 'Ya existe un servicio registrado con este nombre.',
            'price.min' => 'El precio no puede ser un valor negativo.',
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
}
