<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTacheRequest;
use App\Models\Tache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TacheController extends Controller
{
    /**
     * Lister les tâches de l'utilisateur connecté uniquement (paginé).
     */
    public function index()
    {
        return Tache::where('user_id', Auth::id())->paginate(10);
    }

    /**
     * Créer une nouvelle tâche pour l'utilisateur connecté.
     */
    public function store(StoreTacheRequest $request)
    {
        $tache = Tache::create([
            'title'        => $request->title,
            'descriptions' => $request->descriptions,
            'completed'    => $request->boolean('completed', false),
            'user_id'      => Auth::id(),
        ]);

        return response()->json($tache, Response::HTTP_CREATED);
    }

    /**
     * Afficher une tâche spécifique (vérification de propriété).
     */
    public function show(Tache $tache)
    {
        if ($tache->user_id !== Auth::id()) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        return response()->json($tache);
    }

    /**
     * Mettre à jour une tâche (vérification de propriété).
     */
    public function update(Request $request, Tache $tache)
    {
        if ($tache->user_id !== Auth::id()) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $validated = $request->validate([
            'title'        => 'sometimes|required|string|max:255',
            'descriptions' => 'nullable|string',
            'completed'    => 'boolean',
        ]);

        $tache->update($validated);

        return response()->json($tache);
    }

    /**
     * Supprimer une tâche (vérification de propriété).
     */
    public function destroy(Tache $tache)
    {
        if ($tache->user_id !== Auth::id()) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $tache->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Tâche supprimée avec succès',
        ], Response::HTTP_OK);
    }
}