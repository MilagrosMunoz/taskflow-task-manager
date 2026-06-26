<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Listar todas las tareas del usuario autenticado
    public function index()
    {
        $tasks = auth()->user()->tasks;
        return response()->json($tasks, 200);
    }

    // Crear una nueva tarea para el usuario autenticado
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        // Guardar la tarea asignándole automáticamente el ID del usuario logueado
        $task = auth()->user()->tasks()->create($validated);

        return response()->json([
            'message' => 'Tarea creada con éxito',
            'task' => $task
        ], 210);
    }

    // Mostrar el detalle de una tarea específica
    public function show(string $id)
    {
        $task = auth()->user()->tasks()->find($id);

        if (!$task) {
            return response()->json(['message' => 'Tarea no encontrada o no autorizada'], 404);
        }

        return response()->json($task, 200);
    }

    // Actualizar una tarea existente
    public function update(Request $request, string $id)
    {
        $task = auth()->user()->tasks()->find($id);

        if (!$task) {
            return response()->json(['message' => 'Tarea no encontrada o no autorizada'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,in_progress,completed',
            'priority' => 'sometimes|required|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return response()->json([
            'message' => 'Tarea actualizada con éxito',
            'task' => $task
        ], 200);
    }

    // Eliminar una tarea
    public function destroy(string $id)
    {
        $task = auth()->user()->tasks()->find($id);

        if (!$task) {
            return response()->json(['message' => 'Tarea no encontrada o no autorizada'], 404);
        }

        $task->delete();

        return response()->json(['message' => 'Tarea eliminada con éxito'], 200);
    }
}
