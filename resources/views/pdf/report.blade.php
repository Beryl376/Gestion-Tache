<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport d'Activité</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #1e293b; margin: 0; padding: 40px; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #2563eb; margin: 0; font-size: 24px; }
        .header p { color: #64748b; margin: 5px 0 0 0; font-size: 14px; }
        .section { margin-bottom: 30px; }
        .section-title { font-size: 18px; font-weight: bold; color: #334155; margin-bottom: 15px; border-left: 4px solid #2563eb; padding-left: 10px; }
        .ai-analysis { background-color: #f8fafc; border-radius: 8px; padding: 20px; font-style: normal; line-height: 1.6; }
        .ai-analysis h3 { margin-top: 0; color: #2563eb; font-size: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
        .task-list { width: 100%; border-collapse: collapse; }
        .task-list th { text-align: left; background-color: #f1f5f9; padding: 10px; font-size: 12px; color: #475569; }
        .task-list td { padding: 10px; border-bottom: 1px solid #e2e8f0; font-size: 12px; }
        .status { padding: 3px 8px; border-radius: 12px; font-size: 10px; font-weight: bold; }
        .status-done { background-color: #dcfce7; color: #166534; }
        .status-todo { background-color: #f1f5f9; color: #475569; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport d'Activité Personnel</h1>
        <p>Généré le {{ now()->format('d/m/Y à H:i') }} pour {{ $user->name }}</p>
    </div>

    <div class="section">
        <div class="section-title">Analyse Stratégique</div>
        <div class="ai-analysis">
            {!! $analysis !!}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Détail des Tâches</div>
        <table class="task-list">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->descriptions ?? '-' }}</td>
                    <td>
                        <span class="status {{ $task->completed ? 'status-done' : 'status-todo' }}">
                            {{ $task->completed ? 'Terminée' : 'À faire' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        TaskSync — Votre assistant productivité intelligent
    </div>
</body>
</html>
