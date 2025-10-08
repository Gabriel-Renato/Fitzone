<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Exercise::query();

        // Filtrar por grupo muscular
        if ($request->has('muscle_group')) {
            $query->where('muscle_group', $request->muscle_group);
        }

        // Busca por nome
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $exercises = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $exercises
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'muscle_group' => 'required|string|max:255',
            'equipment' => 'nullable|string|max:255',
            'video_url' => 'nullable|url',
            'image_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $exercise = Exercise::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Exercício criado com sucesso',
            'data' => $exercise
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $exercise = Exercise::find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Exercício não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $exercise
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $exercise = Exercise::find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Exercício não encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'muscle_group' => 'sometimes|required|string|max:255',
            'equipment' => 'nullable|string|max:255',
            'video_url' => 'nullable|url',
            'image_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $exercise->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Exercício atualizado com sucesso',
            'data' => $exercise
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $exercise = Exercise::find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Exercício não encontrado'
            ], 404);
        }

        $exercise->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exercício deletado com sucesso'
        ]);
    }

    /**
     * Get muscle groups
     */
    public function muscleGroups()
    {
        $muscleGroups = Exercise::distinct()->pluck('muscle_group');

        return response()->json([
            'success' => true,
            'data' => $muscleGroups
        ]);
    }
}