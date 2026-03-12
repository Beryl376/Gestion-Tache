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

        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('completed', true)->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

        // Préparation du prompt pour Llama 3
        $taskListString = $tasks->map(function ($t) {
            $status = $t->completed ? 'Terminée' : 'En cours';
            return "- {$t->title} ({$status}) : {$t->descriptions}";
        })->implode("\n");

        $prompt = "En tant qu'analyste de performance executive, analyse le portefeuille de tâches suivant pour l'utilisateur {$user->name}. 
        
        DONNÉES CLÉS :
        - Tâches totales : {$totalTasks}
        - Complétées : {$completedTasks}
        - Taux d'achèvement : {$completionRate}%

        CONSIGNES DE RÉDACTION :
        Rédige un rapport au format Markdown, professionnel et analytique, structuré exactement comme suit :
        1. SYNTHÈSE EXÉCUTIVE (Vue d'ensemble de la situation actuelle)
        2. ANALYSE DES RISQUES ET BLOCAGES (Identification des éléments qui freinent l'activité)
        3. RECOMMANDATIONS STRATÉGIQUES (Actions suggérées à court et moyen terme)

        Utilise un ton formel, executive et factuel. Voici le détail des tâches :\n\n{$taskListString}";

        // Appel à l'IA (Utilisation de Groq par défaut ou Mock si pas de clé)
        $analysis = $this->getAiAnalysis($prompt);

        // Utilisation de Parsedown pour le rendu Markdown
        $parsedown = new \Parsedown();
        $analysisHtml = $parsedown->text($analysis);

        // Génération du PDF
        $pdf = Pdf::loadView('pdf.report', [
            'user' => $user,
            'tasks' => $tasks,
            'analysis' => $analysisHtml
        ]);

        $timestamp = now()->format('Ymd_His');
        return $pdf->download("rapport_executive_{$user->name}_{$timestamp}.pdf");
    }

    private function getAiAnalysis($prompt)
    {
        $apiKey = config('services.groq.key');

        if (!$apiKey || $apiKey === 'your-api-key') {
            return "### 1. SYNTHÈSE EXÉCUTIVE\n" .
                "L'analyse actuelle indique que vous gérez un total de " . Auth::user()->taches->count() . " tâches. Le flux de travail semble constant mais nécessite une optimisation de la clôture des dossiers.\n\n" .
                "### 2. ANALYSE DES RISQUES ET BLOCAGES\n" .
                "- **Saturation cognitive** : Le nombre de tâches en cours peut freiner la réactivité.\n" .
                "- **Absence de priorisation claire** : Risque de dispersion sur des tâches à faible valeur ajoutée.\n\n" .
                "### 3. RECOMMANDATIONS STRATÉGIQUES\n" .
                "- **Application de la règle des 2 minutes** : Traitez immédiatement ce qui est rapide.\n" .
                "- **Revue hebdomadaire** : Purgez les tâches obsolètes pour clarifier la vision stratégique.\n\n" .
                "**";
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
        }
        catch (\Exception $e) {
            return "Erreur lors de la communication avec l'IA. " . $e->getMessage();
        }

        return "L'IA a rencontré un problème pour analyser vos tâches.";
    }
}
