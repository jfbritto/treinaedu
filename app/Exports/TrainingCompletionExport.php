<?php

namespace App\Exports;

use App\Models\TrainingView;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TrainingCompletionExport implements FromQuery, WithHeadings, WithMapping
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = TrainingView::with(['user', 'training'])
            ->limit(1000);

        if ($this->request->filled('training_id')) {
            $query->where('training_id', $this->request->training_id);
        }

        if ($this->request->filled('group_id')) {
            $groupUserIds = \App\Models\Group::find($this->request->group_id)
                ?->users()->pluck('users.id') ?? collect();
            $query->whereIn('user_id', $groupUserIds);
        }

        if ($this->request->filled('status')) {
            if ($this->request->status === 'completed') {
                $query->whereNotNull('completed_at');
            } else {
                $query->whereNull('completed_at');
            }
        }

        if ($this->request->filled('date_from')) {
            $query->where('created_at', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $query->where('created_at', '<=', $this->request->date_to);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Funcionário', 'Treinamento', 'Progresso (%)', 'Status', 'Data de Conclusão'];
    }

    public function map($view): array
    {
        return [
            $view->user->name ?? 'N/A',
            $view->training->title ?? 'N/A',
            $view->progress_percent . '%',
            $view->completed_at ? 'Concluído' : 'Pendente',
            $view->completed_at?->format('d/m/Y') ?? '-',
        ];
    }
}
