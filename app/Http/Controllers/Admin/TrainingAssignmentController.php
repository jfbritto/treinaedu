<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Training;
use App\Models\TrainingAssignment;
use Illuminate\Http\Request;

class TrainingAssignmentController extends Controller
{
    public function index()
    {
        $assignments = TrainingAssignment::with(['training', 'group'])
            ->paginate(15);

        return view('admin.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $trainings = Training::where('active', true)->get();
        $groups = Group::all();
        return view('admin.assignments.create', compact('trainings', 'groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'training_id' => 'required|exists:trainings,id',
            'group_ids' => 'required|array|min:1',
            'group_ids.*' => 'exists:groups,id',
            'due_date' => 'nullable|date|after:today',
        ]);

        foreach ($request->group_ids as $groupId) {
            TrainingAssignment::firstOrCreate(
                ['training_id' => $request->training_id, 'group_id' => $groupId],
                [
                    'company_id' => auth()->user()->company_id,
                    'due_date' => $request->due_date,
                ]
            );
        }

        return redirect()->route('training-assignments.index')
            ->with('success', 'Treinamento atribuído aos grupos.');
    }

    public function destroy(TrainingAssignment $trainingAssignment)
    {
        if ((int) $trainingAssignment->company_id !== (int) auth()->user()->company_id) {
            abort(403);
        }
        $trainingAssignment->delete();
        return back()->with('success', 'Atribuição removida.');
    }
}
