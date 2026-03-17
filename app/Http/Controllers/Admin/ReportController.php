<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TrainingCompletionExport;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Training;
use App\Models\TrainingView;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $trainings = Training::all();
        $groups = Group::all();

        $query = TrainingView::with(['user', 'training']);

        if ($request->filled('training_id')) {
            $query->where('training_id', $request->training_id);
        }

        if ($request->filled('group_id')) {
            $groupUserIds = Group::find($request->group_id)
                ?->users()->pluck('users.id') ?? collect();
            $query->whereIn('user_id', $groupUserIds);
        }

        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->whereNotNull('completed_at');
            } else {
                $query->whereNull('completed_at');
            }
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $views = $query->paginate(15);

        return view('admin.reports.index', compact('views', 'trainings', 'groups'));
    }

    public function exportPdf(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = TrainingView::with(['user', 'training'])
            ->where('company_id', $companyId)
            ->limit(1000);

        if ($request->filled('training_id')) {
            $query->where('training_id', $request->training_id);
        }
        if ($request->filled('group_id')) {
            $groupUserIds = Group::find($request->group_id)
                ?->users()->pluck('users.id') ?? collect();
            $query->whereIn('user_id', $groupUserIds);
        }
        if ($request->filled('status')) {
            $request->status === 'completed'
                ? $query->whereNotNull('completed_at')
                : $query->whereNull('completed_at');
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $views = $query->get();

        $pdf = Pdf::loadView('admin.reports.pdf', compact('views'));
        return $pdf->download('relatorio-treinamentos.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new TrainingCompletionExport($request), 'relatorio-treinamentos.xlsx');
    }
}
