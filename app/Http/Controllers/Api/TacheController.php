<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TacheController extends Controller
{
    /**
     * Lister les tâches de l'utilisateur connecté uniquement.
     */
    public function index()
    {
        $taches = Tache::where('user_id', Auth::id())->get();

        return response()->json($taches);
    }

    /**
     * Créer une nouvelle tâche pour l'utilisateur connecté.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'descriptions' => 'nullable|string',
            'completed'    => 'boolean',
        ]);

        $tache = Tache::create([
            'title'        => $request->title,
            'descriptions' => $request->descriptions,
            'completed'    => $request->boolean('completed', false),
            'user_id'      => Auth::id(),
        ]);

        return response()->json($tache, 201);
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

        // N'autoriser que les champs validés (évite l'injection de user_id, etc.)
        $tache->update($validated);

        return response()->json($tache);
    }

    /**
     * Supprimer une tâche (vérification de propriété). Convention Laravel: destroy.
     */
    public function destroy(Tache $tache)
    {
        if ($tache->user_id !== Auth::id()) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $tache->delete();

        return response()->json(null, 204);
    }
}