<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PersonalClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    /**
     * Listar clientes do personal
     */
    public function index(Request $request)
    {
        $personal = $request->user();

        if (!$personal->isPersonal()) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas personal trainers podem acessar esta rota'
            ], 403);
        }

        $clientes = $personal->clientes()
            ->wherePivot('status', 'ativo')
            ->withCount('workoutLogs')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $clientes
        ]);
    }

    /**
     * Criar novo cliente
     */
    public function store(Request $request)
    {
        $personal = $request->user();

        if (!$personal->isPersonal()) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas personal trainers podem criar clientes'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'goals' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Criar cliente
        $client = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'cliente',
            'phone' => $request->phone,
            'personal_id' => $personal->id,
        ]);

        // Criar relacionamento
        PersonalClient::create([
            'personal_id' => $personal->id,
            'client_id' => $client->id,
            'status' => 'ativo',
            'goals' => $request->goals,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cliente criado com sucesso',
            'data' => $client
        ], 201);
    }

    /**
     * Detalhes do cliente
     */
    public function show(Request $request, $id)
    {
        $personal = $request->user();

        if (!$personal->isPersonal()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado'
            ], 403);
        }

        $client = User::with([
            'workoutPlans.workout.exercises', 
            'workoutLogs.workout'
        ])->findOrFail($id);

        // Verificar se o cliente pertence ao personal
        if ($client->personal_id !== $personal->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente não encontrado'
            ], 404);
        }

        // Carregar dados do relacionamento personal-cliente (goals, observations)
        $personalClientData = PersonalClient::where('personal_id', $personal->id)
            ->where('client_id', $client->id)
            ->first();

        if ($personalClientData) {
            $client->pivot = $personalClientData;
        }

        return response()->json([
            'success' => true,
            'data' => $client
        ]);
    }

    /**
     * Atualizar cliente
     */
    public function update(Request $request, $id)
    {
        $personal = $request->user();

        if (!$personal->isPersonal()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado'
            ], 403);
        }

        $client = User::findOrFail($id);

        if ($client->personal_id !== $personal->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente não encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $client->update($request->only(['name', 'email', 'phone']));

        return response()->json([
            'success' => true,
            'message' => 'Cliente atualizado com sucesso',
            'data' => $client
        ]);
    }

    /**
     * Desativar cliente
     */
    public function destroy(Request $request, $id)
    {
        $personal = $request->user();

        if (!$personal->isPersonal()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado'
            ], 403);
        }

        $client = User::findOrFail($id);

        if ($client->personal_id !== $personal->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente não encontrado'
            ], 404);
        }

        // Desativar relacionamento ao invés de deletar
        PersonalClient::where('personal_id', $personal->id)
            ->where('client_id', $client->id)
            ->update(['status' => 'inativo', 'end_date' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Cliente desativado com sucesso'
        ]);
    }
}