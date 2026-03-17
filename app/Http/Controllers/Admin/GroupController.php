<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::withCount('users')->paginate(15);
        return view('admin.groups.index', compact('groups'));
    }

    public function create()
    {
        $users = User::where('company_id', auth()->user()->company_id)
            ->whereIn('role', ['instructor', 'employee'])
            ->orderBy('name')
            ->get();
        return view('admin.groups.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id,company_id,' . auth()->user()->company_id,
        ]);

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('users')) {
            $group->users()->sync($request->users);
        }

        return redirect()->route('groups.index')->with('success', 'Grupo criado com sucesso.');
    }

    public function show(Group $group)
    {
        $this->authorizeCompany($group);
        $group->load('users');
        return view('admin.groups.show', compact('group'));
    }

    public function edit(Group $group)
    {
        $this->authorizeCompany($group);
        $group->load('users');
        $users = User::where('company_id', auth()->user()->company_id)
            ->whereIn('role', ['instructor', 'employee'])
            ->orderBy('name')
            ->get();
        return view('admin.groups.edit', compact('group', 'users'));
    }

    public function update(Request $request, Group $group)
    {
        $this->authorizeCompany($group);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id,company_id,' . auth()->user()->company_id,
        ]);

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $group->users()->sync($request->users ?? []);

        return redirect()->route('groups.index')->with('success', 'Grupo atualizado.');
    }

    public function destroy(Group $group)
    {
        $this->authorizeCompany($group);
        $group->delete();
        return redirect()->route('groups.index')->with('success', 'Grupo removido.');
    }

    private function authorizeCompany(Group $group): void
    {
        if ((int) $group->company_id !== (int) auth()->user()->company_id) {
            abort(403);
        }
    }
}
