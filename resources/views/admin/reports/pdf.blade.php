<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Treinamentos</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            font-size: 18px;
            color: #1a1a2e;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 11px;
            color: #666;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        thead tr {
            background-color: #2563eb;
            color: #fff;
        }
        thead th {
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        tbody tr:nth-child(even) {
            background-color: #f3f4f6;
        }
        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
        tbody td {
            padding: 7px 10px;
            font-size: 11px;
            border-bottom: 1px solid #e5e7eb;
        }
        .badge-completed {
            background-color: #d1fae5;
            color: #065f46;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
        }
        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
            color: #999;
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>Relatório de Treinamentos</h1>
    <p class="subtitle">Gerado em {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Funcionário</th>
                <th>Treinamento</th>
                <th>Progresso</th>
                <th>Status</th>
                <th>Data de Conclusão</th>
            </tr>
        </thead>
        <tbody>
            @forelse($views as $view)
                <tr>
                    <td>{{ $view->user->name ?? 'N/A' }}</td>
                    <td>{{ $view->training->title ?? 'N/A' }}</td>
                    <td>{{ $view->progress_percent }}%</td>
                    <td>
                        @if($view->completed_at)
                            <span class="badge-completed">Concluído</span>
                        @else
                            <span class="badge-pending">Pendente</span>
                        @endif
                    </td>
                    <td>{{ $view->completed_at?->format('d/m/Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #999; padding: 20px;">
                        Nenhum registro encontrado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total de registros: {{ $views->count() }}
    </div>
</body>
</html>
