<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function generate(Request $request)
    {
        $user = Auth::user();
        $tasks = Tache::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'Aucune tâche trouvée pour générer un rapport.'], 400);
        }

        // Préparation du prompt pour Llama 3
        $taskListString = $tasks->map(function ($t) {
            $status = $t->completed ? 'Terminée' : 'En cours';
            return "- {$t->title} ({$status}) : {$t->descriptions}";
        })->implode("\n");

        $prompt = "En tant qu'assistant de productivité, analyse la liste de tâches suivante de l'utilisateur {$user->name}. 
        Rédige un rapport synthétique, sobre et encourageant (environ 200 mots). 
        Inclus : un résumé de l'activité, une analyse de la thématique dominante, et 3 conseils personnalisés pour améliorer sa productivité.
        Utilise un ton professionnel. Voici les tâches :\n\n{$taskListString}";

        // Appel à l'IA (Utilisation de Groq par défaut ou Mock si pas de clé)
        $analysis = $this->getAiAnalysis($prompt);

        // Génération du PDF
        $pdf = Pdf::loadView('pdf.report', [
            'user' => $user,
            'tasks' => $tasks,
            'analysis' => $analysis
        ]);

        return $pdf->download("rapport_productivite_{$user->name}.pdf");
    }

    private function getAiAnalysis($prompt)
    {
        $apiKey = config('services.groq.key');

        if (!$apiKey || $apiKey === 'your-api-key') {
            return "Note : L'API Llama 3 n'est pas encore configurée. Voici une analyse par défaut basée sur vos " . Auth::user()->taches->count() . " tâches. Vous semblez avoir un bon équilibre entre vos différentes activités. Continuez ainsi ! (Configurez GROQ_API_KEY dans votre .env pour une analyse réelle)";
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama3-8b-8192',
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es un expert en productivité.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7
            ]);

            if ($response->successful()) {
                return $response->json()['choices'][0]['message']['content'];
            }
        } catch (\Exception $e) {
            return "Erreur lors de la communication avec l'IA. " . $e->getMessage();
        }

        return "L'IA a rencontré un problème pour analyser vos tâches.";
    }
}
