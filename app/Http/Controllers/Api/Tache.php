<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Tache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TacheController extends Controller
{
    public function index()
    {
        return Tache::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'completed' => 'default: false',
        ]);
        $post = new Tache();
        $post->title = $request->title;
        $post->description = $request->description;
        $post->completed = $request->completed;
        $post->user_id = Auth::id();
        $post->save();
        // $Tache = Tache::create($request->all());

        return response()->json($post, 201);
    }

    public function show(Tache $Tache)
    {
        return $Tache;
    }

    public function update(Request $request, Tache $Tache)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'completed' => 'boolean',
        ]);

        $Tache->update($request->all());

        return response()->json($Tache);
    }

    public function delete(Request $request, Tache $Tache)
    {
        $Tache->delete();

        return response()->json(null, 204);
    }
}