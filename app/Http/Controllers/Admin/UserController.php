<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Group;
use App\Models\User;
use App\Notifications\UserInvitedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('company_id', auth()->user()->company_id)
            ->where('id', '!=', auth()->id())
            ->with('groups');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        // Stats gerais (não filtrados)
        $companyUsers = User::where('company_id', auth()->user()->company_id)
            ->where('id', '!=', auth()->id());
        $totalUsers = $companyUsers->count();
        $totalActive = (clone $companyUsers)->where('active', true)->count();
        $totalInstructors = (clone $companyUsers)->where('role', 'instructor')->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'totalActive', 'totalInstructors'));
    }

    public function create()
    {
        $groups = Group::all();
        return view('admin.users.create', compact('groups'));
    }

    public function store(StoreUserRequest $request)
    {
        $admin = auth()->user();
        $company = $admin->company()->with('subscription.plan')->first();

        if ($company->hasReachedUserLimit()) {
            return back()->with('error', 'Limite de usuários do plano atingido.');
        }

        // Senha aleatória forte - usuário definirá a própria via link de convite
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(Str::random(40)),
            'company_id' => $company->id,
            'role' => $request->role,
            'active' => true,
            'invited_at' => now(),
        ]);

        if ($request->has('groups')) {
            $user->groups()->sync($request->groups);
        }

        // Envia convite com link para o usuário definir sua própria senha
        $token = Password::broker('invites')->createToken($user);
        $user->notify(new UserInvitedNotification($token, $admin, $company));

        return redirect()->route('users.index')
            ->with('success', "Usuário criado! Um e-mail de convite foi enviado para {$user->email}.");
    }

    public function resendInvite(User $user)
    {
        $this->authorizeCompany($user);

        if (!$user->isPendingInvite()) {
            return back()->with('error', 'Este usuário já definiu sua senha.');
        }

        $admin = auth()->user();
        $company = $admin->company;

        $token = Password::broker('invites')->createToken($user);
        $user->notify(new UserInvitedNotification($token, $admin, $company));

        $user->update(['invited_at' => now()]);

        return back()->with('success', "Convite reenviado para {$user->email}.");
    }

    public function show(User $user)
    {
        $this->authorizeCompany($user);

        // Get assigned trainings with progress
        $assignedTrainings = $user->assignedTrainings()
            ->get()
            ->map(function ($training) use ($user) {
                $views = $user->trainingViews()->where('training_id', $training->id)->get();
                $completed = $views->where('completed_at', '!=', null)->count();
                $total = $views->count();
                $latestView = $views->sortByDesc('created_at')->first();

                $progressPercent = $latestView ? (int) $latestView->progress_percent : 0;
                $isCompleted = $completed > 0;

                // Determine readable status
                $status = match (true) {
                    $isCompleted => 'completed',
                    $progressPercent >= 100 => 'pending_completion', // lessons done, awaiting quiz or "Concluir"
                    $total > 0 => 'in_progress',
                    default => 'not_started',
                };

                return [
                    'id' => $training->id,
                    'title' => $training->title,
                    'description' => $training->description,
                    'duration_minutes' => $training->calculatedDuration(),
                    'total_views' => $total,
                    'completed' => $isCompleted || $progressPercent >= 100,
                    'status' => $status,
                    'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
                    'progress_percent' => $progressPercent,
                    'last_accessed' => $latestView ? $latestView->created_at : null,
                    'completed_at' => $views->where('completed_at', '!=', null)->first()?->completed_at,
                    'due_date' => $training->assignments->first()?->due_date,
                    'mandatory' => $training->assignments->first()?->mandatory ?? false,
                ];
            })
            ->sortByDesc('last_accessed')
            ->values();

        // Stats
        $totalAssigned = $assignedTrainings->count();
        $totalCompleted = $assignedTrainings->where('completed', true)->count();
        $completionRate = $totalAssigned > 0 ? round(($totalCompleted / $totalAssigned) * 100, 2) : 0;
        $avgProgress = $totalAssigned > 0 ? round($assignedTrainings->avg('progress_percent'), 2) : 0;

        return view('admin.users.show', compact(
            'user',
            'assignedTrainings',
            'totalAssigned',
            'totalCompleted',
            'completionRate',
            'avgProgress'
        ));
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

        return redirect()->route('users.show', $user)->with('success', 'Usuário atualizado.');
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
