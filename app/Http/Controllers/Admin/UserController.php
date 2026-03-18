<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('company_id', auth()->user()->company_id)
            ->where('id', '!=', auth()->id())
            ->with('groups')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $groups = Group::all();
        return view('admin.users.create', compact('groups'));
    }

    public function store(StoreUserRequest $request)
    {
        $company = auth()->user()->company()->with('subscription.plan')->first();

        if ($company->hasReachedUserLimit()) {
            return back()->with('error', 'Limite de usuários do plano atingido.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $company->id,
            'role' => $request->role,
        ]);

        if ($request->has('groups')) {
            $user->groups()->sync($request->groups);
        }

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user)
    {
        if ($user->is(auth()->user())) {
            return redirect()->route('profile.edit');
        }
        $this->authorizeCompany($user);
        $groups = Group::where('company_id', auth()->user()->company_id)->get();
        return view('admin.users.edit', compact('user', 'groups'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorizeCompany($user);

        if ($user->is(auth()->user())) {
            abort(403, 'Você não pode editar seu próprio perfil por aqui.');
        }

        $data = $request->only('name', 'email', 'role', 'active');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->has('groups')) {
            $user->groups()->sync($request->groups);
        }

        return redirect()->route('users.index')->with('success', 'Usuário atualizado.');
    }

    public function destroy(User $user)
    {
        $this->authorizeCompany($user);

        if ($user->is(auth()->user())) {
            abort(403, 'Você não pode remover sua própria conta.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuário removido.');
    }

    private function authorizeCompany(User $user): void
    {
        if ((int) $user->company_id !== (int) auth()->user()->company_id) {
            abort(403);
        }
    }
}
