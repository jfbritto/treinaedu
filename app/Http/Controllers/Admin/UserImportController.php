<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendBulkInviteJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserImportController extends Controller
{
    public function show()
    {
        $company = auth()->user()->company;
        $groups = $company->groups()->orderBy('name')->get();

        return view('admin.users.import', compact('groups'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048',
        ]);

        $file = $request->file('file');
        $rows = $this->parseFile($file);

        if (empty($rows)) {
            return back()->with('error', 'O arquivo está vazio ou não contém dados válidos.');
        }

        $company = auth()->user()->company;
        $groups = $company->groups()->orderBy('name')->get();
        $existingEmails = User::withoutGlobalScopes()->pluck('email')->map(fn ($e) => strtolower($e))->toArray();

        $preview = [];
        foreach ($rows as $i => $row) {
            $name = trim($row['nome'] ?? $row['name'] ?? $row[0] ?? '');
            $email = strtolower(trim($row['email'] ?? $row['e-mail'] ?? $row[1] ?? ''));
            $role = strtolower(trim($row['cargo'] ?? $row['role'] ?? $row['tipo'] ?? $row[2] ?? 'employee'));

            // Normalize role
            if (in_array($role, ['instrutor', 'instructor', 'professor'])) {
                $role = 'instructor';
            } else {
                $role = 'employee';
            }

            $errors = [];
            if (empty($name)) $errors[] = 'Nome obrigatório';
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';
            if (in_array($email, $existingEmails)) $errors[] = 'Email já cadastrado';

            $preview[] = [
                'row' => $i + 1,
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'errors' => $errors,
                'valid' => empty($errors),
            ];
        }

        session(['import_preview' => $preview]);

        return view('admin.users.import-preview', compact('preview', 'groups'));
    }

    public function process(Request $request)
    {
        $preview = session('import_preview');
        if (!$preview) {
            return redirect()->route('users.import')->with('error', 'Nenhum dado para importar. Faça o upload novamente.');
        }

        $request->validate([
            'role_override' => 'nullable|in:employee,instructor',
            'groups' => 'nullable|array',
            'groups.*' => 'exists:groups,id',
            'send_invites' => 'nullable|boolean',
        ]);

        $admin = auth()->user();
        $company = $admin->company()->with('subscription.plan')->first();
        $roleOverride = $request->role_override;
        $sendInvites = $request->boolean('send_invites', true);

        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($preview as $row) {
            if (!$row['valid']) {
                $skipped++;
                continue;
            }

            if ($company->hasReachedUserLimit()) {
                $errors[] = "Linha {$row['row']}: Limite de usuários do plano atingido.";
                break;
            }

            $role = $roleOverride ?: $row['role'];

            try {
                $user = User::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'password' => Hash::make(Str::random(40)),
                    'company_id' => $company->id,
                    'role' => $role,
                    'active' => true,
                    'invited_at' => now(),
                ]);

                if ($request->has('groups')) {
                    $user->groups()->sync($request->groups);
                }

                if ($sendInvites) {
                    SendBulkInviteJob::dispatch($user->id, $admin->id, $company->id);
                }

                $imported++;
            } catch (\Throwable $e) {
                $skipped++;
                $errors[] = "Linha {$row['row']} ({$row['email']}): " . $e->getMessage();
            }
        }

        session()->forget('import_preview');

        $message = "{$imported} usuário(s) importado(s) com sucesso.";
        if ($skipped > 0) $message .= " {$skipped} ignorado(s).";
        if ($sendInvites && $imported > 0) $message .= " Os convites estão sendo enviados por e-mail em segundo plano.";

        return redirect()->route('users.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }

    public function template()
    {
        $csv = "nome,email,cargo\n";
        $csv .= "Maria Silva,maria@empresa.com,employee\n";
        $csv .= "João Santos,joao@empresa.com,instructor\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="modelo-importacao-usuarios.csv"',
        ]);
    }

    private function parseFile($file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        if (in_array($extension, ['csv', 'txt'])) {
            return $this->parseCsv($path);
        }

        // For xlsx/xls, try csv parsing (many users save xlsx as csv)
        return $this->parseCsv($path);
    }

    private function parseCsv(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'r');
        if (!$handle) return [];

        // Detect delimiter
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = str_contains($firstLine, ';') ? ';' : ',';

        $header = null;
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (!$header) {
                $header = array_map(fn ($h) => strtolower(trim($h)), $data);
                // Check if first row looks like data (no header)
                if (filter_var($header[1] ?? '', FILTER_VALIDATE_EMAIL)) {
                    $rows[] = $header;
                    $header = null;
                }
                continue;
            }

            if (count($data) < 2) continue;
            if (empty(trim($data[0] ?? '')) && empty(trim($data[1] ?? ''))) continue;

            $row = $header
                ? array_combine(array_slice($header, 0, count($data)), $data)
                : $data;

            $rows[] = $row;
        }

        fclose($handle);
        return $rows;
    }
}
