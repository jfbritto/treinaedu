<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index()
    {
        $trainings = Training::where('created_by', auth()->id())
            ->latest()
            ->paginate(15);

        return view('instructor.trainings.index', compact('trainings'));
    }

    public function create()
    {
        return view('instructor.trainings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|url',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $training = Training::create([
            'title' => $request->title,
            'description' => $request->description,
            'video_url' => $request->video_url,
            'video_provider' => \App\Models\Training::detectProvider($request->video_url),
            'duration_minutes' => $request->duration_minutes,
            'created_by' => auth()->id(),
            'company_id' => auth()->user()->company_id,
        ]);

        return redirect()->route('instructor.trainings.index')
            ->with('success', 'Treinamento criado com sucesso!');
    }

    public function edit(Training $training)
    {
        $this->authorize('update', $training);

        return view('instructor.trainings.edit', compact('training'));
    }

    public function update(Request $request, Training $training)
    {
        $this->authorize('update', $training);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $training->update([
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
        ]);

        return redirect()->route('instructor.trainings.index')
            ->with('success', 'Treinamento atualizado!');
    }

    public function destroy(Training $training)
    {
        $this->authorize('delete', $training);

        $training->delete();

        return redirect()->route('instructor.trainings.index')
            ->with('success', 'Treinamento removido!');
    }
}
