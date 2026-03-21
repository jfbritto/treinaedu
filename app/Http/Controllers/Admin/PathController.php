<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Path;
use App\Models\Training;
use Illuminate\Http\Request;

class PathController extends Controller
{
    public function index()
    {
        $paths = Path::withCount('trainings')->orderBy('sort_order')->paginate(15);
        return view('admin.paths.index', compact('paths'));
    }

    public function create()
    {
        $trainings = Training::where('active', true)->orderBy('title')->get();
        return view('admin.paths.create', compact('trainings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'active' => 'boolean',
            'trainings' => 'nullable|array',
            'trainings.*' => 'exists:trainings,id,company_id,' . auth()->user()->company_id,
        ]);

        $path = Path::create([
            'title' => $request->title,
            'description' => $request->description,
            'color' => $request->color ?? '#3B82F6',
            'sort_order' => $request->sort_order ?? 0,
            'active' => $request->boolean('active', true),
        ]);

        $this->syncTrainings($path, $request->trainings ?? []);

        return redirect()->route('paths.show', $path)->with('success', 'Trilha criada com sucesso.');
    }

    public function show(Path $path)
    {
        $this->authorizeCompany($path);
        $path->load('trainings');
        return view('admin.paths.show', compact('path'));
    }

    public function edit(Path $path)
    {
        $this->authorizeCompany($path);
        $path->load('trainings');
        $trainings = Training::where('active', true)->orderBy('title')->get();
        return view('admin.paths.edit', compact('path', 'trainings'));
    }

    public function update(Request $request, Path $path)
    {
        $this->authorizeCompany($path);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'active' => 'boolean',
            'trainings' => 'nullable|array',
            'trainings.*' => 'exists:trainings,id,company_id,' . auth()->user()->company_id,
        ]);

        $path->update([
            'title' => $request->title,
            'description' => $request->description,
            'color' => $request->color ?? '#3B82F6',
            'sort_order' => $request->sort_order ?? 0,
            'active' => $request->boolean('active', true),
        ]);

        $this->syncTrainings($path, $request->trainings ?? []);

        return redirect()->route('paths.show', $path)->with('success', 'Trilha atualizada.');
    }

    public function destroy(Path $path)
    {
        $this->authorizeCompany($path);
        $path->delete();
        return redirect()->route('paths.index')->with('success', 'Trilha removida.');
    }

    private function syncTrainings(Path $path, array $trainingIds): void
    {
        $syncData = [];
        foreach ($trainingIds as $index => $id) {
            $syncData[$id] = ['sort_order' => $index];
        }
        $path->trainings()->sync($syncData);
    }

    private function authorizeCompany(Path $path): void
    {
        if ((int) $path->company_id !== (int) auth()->user()->company_id) {
            abort(403);
        }
    }
}
