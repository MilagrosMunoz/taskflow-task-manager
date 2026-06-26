<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // 1. LISTAR TAREAS (Para el GET de Vue)
    public function index()
    {
        // Trae las tareas que pertenezcan al usuario logueado en Neon
        $tasks = Task::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($tasks, 200);
    }

    // 2. CREAR TAREA (Para el POST de Vue)
    public function store(Request $request)
    {
        // Validamos estrictamente lo que viene de Vue
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        // Guardamos en la base de datos Neon
        $task = Task::create([
            'user_id' => Auth::id(), // Enlaza la tarea al usuario actual
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'completed' => false
        ]);

        return response()->json($task, 201);
    }

    // 3. CAMBIAR ESTADO (Para el PUT de Vue)
    public function update(Request $request, $id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);

        $task->update([
            'completed' => $request->completed
        ]);

        return response()->json($task, 200);
    }

    // 4. ELIMINAR TAREA (Para el DELETE de Vue)
    public function destroy($id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Tarea eliminada con éxito'], 200);
    }
}
