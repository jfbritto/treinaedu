# TreinaHub — Design Spec

**Data:** 2026-03-17
**Status:** Aprovado
**Stack:** Laravel 11 + Blade + TailwindCSS + MySQL + DomPDF + Asaas API

---

## 1. Visao Geral

TreinaHub e uma plataforma SaaS de gestao de treinamentos corporativos (LMS). Empresas se cadastram, criam treinamentos com videos externos (YouTube/Vimeo), atribuem a grupos de funcionarios, acompanham conclusao e emitem certificados PDF.

**Publico-alvo:** Hospitais, restaurantes, hoteis e empresas que precisam de certificacao de treinamentos de compliance.

**Modelo de negocio:** Assinatura mensal com 3 planos (Basic, Pro, Enterprise) cobrados via Asaas.

---

## 2. Decisoes Arquiteturais

| Decisao | Escolha |
|---|---|
| Multi-tenancy | Banco unico com `company_id` + Global Scope |
| Onboarding | Self-service (empresa se cadastra sozinha) |
| Conclusao de treinamento | Hibrido (botao aparece apos 90% do video) |
| Quiz | Multipla escolha simples (uma resposta correta) |
| Pagamentos | Recorrente via Asaas com 7 dias de trial |
| Acesso dos tenants | URL unica, identificado pelo login do usuario |
| Personalizacao | Logo + cores primaria/secundaria por empresa |
| Arquitetura | Laravel monolitico + endpoints AJAX pontuais |
| Hosting | Hostgator compartilhada (sem filas, sem Redis, sem workers) |

---

## 3. Arquitetura Geral

### Stack

- Backend: Laravel 11 monolitico
- Frontend: Blade + TailwindCSS
- Banco: MySQL unico
- Auth: Laravel Breeze
- PDF: DomPDF
- Videos: Embed YouTube/Vimeo (nao hospedados)
- Pagamentos: Asaas API (boleto, PIX, cartao)
- Graficos: Chart.js via CDN

### Multi-tenancy

Trait `BelongsToCompany` aplicado em todos os models que precisam de isolamento. Adiciona Global Scope que filtra por `company_id` do usuario logado e preenche automaticamente ao criar registros.

```php
trait BelongsToCompany
{
    protected static function bootBelongsToCompany()
    {
        static::addGlobalScope('company', function ($query) {
            if (auth()->check()) {
                $query->where('company_id', auth()->user()->company_id);
            }
        });

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }
}
```

**Aplicado em:** Group, Training, TrainingAssignment, TrainingView, Quiz, QuizAttempt, Certificate, Subscription, Payment.

**Nao aplicado em:** User (filtrado manualmente), Company, Plan.

**Nota:** QuizQuestion e QuizOption nao possuem `company_id` proprio. Devem SEMPRE ser acessados via relacionamento do Quiz pai (que e scoped). Controllers nunca devem fazer `QuizQuestion::find($id)` diretamente. Policies devem garantir que edicao/exclusao de perguntas passa pelo Quiz, validando ownership.

### Roles

Quatro roles: `super_admin`, `admin`, `instructor`, `employee`.

- `super_admin`: Dono do SaaS, opera fora do escopo de empresa.
- `admin`: Gerente da empresa, acessa tudo da sua empresa.
- `instructor`: Cria e gerencia treinamentos.
- `employee`: Assiste treinamentos e recebe certificados.

### Middleware

- `RoleMiddleware`: Valida role por grupo de rotas.
- `CheckSubscription`: Verifica assinatura ativa ou trial antes de permitir acesso. Redireciona para pagina de renovacao se expirada.
- `InjectCompanyTheme`: Disponibiliza logo e cores da empresa para as views.

### Estrutura de Pastas

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/              # Controllers do admin da empresa
│   │   ├── Employee/           # Controllers do funcionario
│   │   ├── Instructor/         # Controllers do instrutor
│   │   ├── SuperAdmin/         # Controllers do dono do SaaS
│   │   └── Auth/               # Breeze auth controllers
│   ├── Middleware/
│   │   ├── RoleMiddleware.php
│   │   ├── CheckSubscription.php
│   │   └── InjectCompanyTheme.php
│   └── Requests/               # Form Requests para validacao
├── Models/
│   ├── Traits/
│   │   └── BelongsToCompany.php
│   ├── User.php
│   ├── Company.php
│   ├── Training.php
│   ├── Group.php
│   ├── TrainingAssignment.php
│   ├── TrainingView.php
│   ├── Quiz.php
│   ├── QuizQuestion.php
│   ├── QuizOption.php
│   ├── QuizAttempt.php
│   ├── Certificate.php
│   ├── Plan.php
│   ├── Subscription.php
│   └── Payment.php
├── Policies/                    # Authorization policies
├── Services/
│   ├── AsaasService.php
│   ├── CertificateService.php
│   └── VideoProgressService.php
resources/
├── views/
│   ├── admin/
│   ├── employee/
│   ├── instructor/
│   ├── super-admin/
│   ├── components/
│   │   ├── layout/
│   │   │   ├── app.blade.php
│   │   │   └── guest.blade.php
│   │   ├── ui/
│   │   │   ├── card.blade.php
│   │   │   ├── table.blade.php
│   │   │   ├── modal.blade.php
│   │   │   ├── alert.blade.php
│   │   │   └── video-player.blade.php
│   │   └── forms/
│   │       ├── input.blade.php
│   │       ├── select.blade.php
│   │       └── button.blade.php
│   └── layouts/
│       └── certificate.blade.php
```

---

## 4. Schema do Banco de Dados

### companies

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| name | varchar(255) | Nome da empresa |
| slug | varchar(255) unique | Identificador unico |
| asaas_customer_id | varchar(255) nullable | ID do cliente no Asaas |
| logo_path | varchar(255) nullable | Caminho do logo |
| primary_color | varchar(7) default `#3B82F6` | Cor primaria (hex) |
| secondary_color | varchar(7) default `#1E40AF` | Cor secundaria (hex) |
| created_at / updated_at | timestamps | |
| deleted_at | timestamp nullable | Soft delete |

### users

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| company_id | bigint FK nullable | Null para super_admin |
| name | varchar(255) | |
| email | varchar(255) unique | |
| password | varchar(255) | |
| role | enum: super_admin, admin, instructor, employee | |
| active | boolean default true | |
| created_at / updated_at | timestamps | |
| deleted_at | timestamp nullable | Soft delete |

### groups

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| company_id | bigint FK | |
| name | varchar(255) | |
| description | text nullable | |
| created_at / updated_at | timestamps | |

### group_user (pivot)

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| group_id | bigint FK | |
| user_id | bigint FK | |
| created_at / updated_at | timestamps | Para auditoria de quando usuario entrou no grupo |

### trainings

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| company_id | bigint FK | |
| created_by | bigint FK (users) | Admin ou instrutor |
| title | varchar(255) | |
| description | text nullable | |
| video_url | varchar(500) | URL YouTube ou Vimeo |
| video_provider | enum: youtube, vimeo | Detectado automaticamente |
| duration_minutes | int unsigned | |
| passing_score | int unsigned nullable | Nota minima do quiz (%) |
| has_quiz | boolean default false | |
| active | boolean default true | |
| created_at / updated_at | timestamps | |
| deleted_at | timestamp nullable | Soft delete (certificados dependem do training) |

### training_assignments

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| company_id | bigint FK | |
| training_id | bigint FK | |
| group_id | bigint FK | |
| due_date | date nullable | Prazo opcional |
| created_at / updated_at | timestamps | |
| **unique** | (training_id, group_id) | |

### training_views

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| company_id | bigint FK | |
| training_id | bigint FK | |
| user_id | bigint FK | |
| progress_percent | tinyint unsigned default 0 | |
| started_at | timestamp nullable | |
| completed_at | timestamp nullable | |
| created_at / updated_at | timestamps | |
| **unique** | (training_id, user_id) | |

### quizzes

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| training_id | bigint FK unique | Um quiz por treinamento |
| company_id | bigint FK | |
| created_at / updated_at | timestamps | |

### quiz_questions

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| quiz_id | bigint FK | |
| question | text | |
| order | int unsigned default 0 | |
| created_at / updated_at | timestamps | |

### quiz_options

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| quiz_question_id | bigint FK | |
| option_text | varchar(500) | |
| is_correct | boolean default false | |
| order | int unsigned default 0 | |

### quiz_attempts

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| quiz_id | bigint FK | |
| user_id | bigint FK | |
| company_id | bigint FK | |
| score | tinyint unsigned | Nota obtida (%) |
| passed | boolean | |
| completed_at | timestamp | |
| created_at / updated_at | timestamps | |

### certificates

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| company_id | bigint FK | |
| user_id | bigint FK | |
| training_id | bigint FK | |
| certificate_code | varchar(20) unique | Codigo de verificacao |
| pdf_path | varchar(255) | |
| generated_at | timestamp | |
| created_at / updated_at | timestamps | |

### plans

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| name | varchar(100) | Basic, Pro, Enterprise |
| price | decimal(10,2) | Valor mensal (R$) |
| max_users | int unsigned nullable | Null = ilimitado |
| max_trainings | int unsigned nullable | Null = ilimitado |
| features | json nullable | |
| active | boolean default true | |
| created_at / updated_at | timestamps | |

### subscriptions

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| company_id | bigint FK unique | |
| plan_id | bigint FK | |
| asaas_subscription_id | varchar(255) nullable | |
| status | enum: trial, active, past_due, cancelled, expired | |
| trial_ends_at | timestamp nullable | |
| current_period_start | timestamp nullable | |
| current_period_end | timestamp nullable | |
| created_at / updated_at | timestamps | |

### payments

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| company_id | bigint FK | |
| subscription_id | bigint FK | |
| asaas_payment_id | varchar(255) nullable | |
| amount | decimal(10,2) | |
| status | enum: pending, confirmed, received, overdue, refunded | |
| payment_method | enum: boleto, pix, credit_card | |
| paid_at | timestamp nullable | |
| due_date | date | |
| created_at / updated_at | timestamps | |

### Indexes

- `users`: index em `(company_id, role)`
- `training_views`: index em `(company_id, user_id)`
- `training_assignments`: index em `(company_id, group_id)`
- `certificates`: index em `certificate_code`
- `payments`: index em `(company_id, status)`

---

## 5. Models e Relacionamentos

```
Company
├── hasMany: Users, Groups, Trainings, Certificates
├── hasOne: Subscription
│
User
├── belongsTo: Company
├── belongsToMany: Groups (pivot group_user)
├── hasMany: TrainingViews, QuizAttempts, Certificates
│
Group
├── belongsTo: Company
├── belongsToMany: Users
├── belongsToMany: Trainings (pivot training_assignments)
│
Training
├── belongsTo: Company, User (created_by)
├── hasOne: Quiz
├── hasMany: TrainingViews, TrainingAssignments, Certificates
├── belongsToMany: Groups (pivot training_assignments)
│
Quiz
├── belongsTo: Training
├── hasMany: QuizQuestions, QuizAttempts
│
QuizQuestion
├── belongsTo: Quiz
├── hasMany: QuizOptions
│
Subscription
├── belongsTo: Company, Plan
├── hasMany: Payments
```

### Computed Properties

**User:**
- `assignedTrainings()` — treinamentos via grupos do usuario
- `isAdmin()`, `isInstructor()`, `isEmployee()`, `isSuperAdmin()`

**Training:**
- `completionRate()` — % de usuarios que completaram

**Company:**
- `isOnTrial()` — subscription.status == trial && trial_ends_at > now
- `hasActiveSubscription()` — trial ativo OU subscription ativa
- `hasReachedUserLimit()` — users count vs plan.max_users

---

## 6. Services

### VideoProgressService

- Recebe AJAX com `training_id` e `progress_percent`
- Cria ou atualiza `training_views` (upsert por training_id + user_id)
- Se `started_at` null, preenche com now
- Quando `progress_percent >= 90`, permite botao "Marcar como concluido"
- Ao marcar como concluido, preenche `completed_at`

### CertificateService

- Verifica conclusao: `completed_at` preenchido
- Se `has_quiz`, verifica `quiz_attempt.passed == true`
- Gera codigo unico: `TH-{ANO}-{4 chars}-{4 chars}` (ex: TH-2026-A3F2-9K1B)
- Renderiza PDF com DomPDF usando template Blade landscape
- Salva em `storage/app/certificates/{company_id}/`
- Cria registro em `certificates`

### AsaasService

- `createCustomer(Company)` — cria cliente no Asaas
- `createSubscription(Company, Plan, paymentMethod)` — cria assinatura recorrente
- `handleWebhook(payload)` — processa webhooks (pagamento confirmado, atrasado, cancelado)
- `cancelSubscription(Subscription)` — cancela no Asaas e atualiza local
- Processamento idempotente (verifica se ja processado antes de atualizar)

---

## 7. Fluxos Principais

### 7.1 Cadastro de empresa (onboarding)

1. Visitante acessa `/register`
2. Preenche: nome da empresa, nome do admin, email, senha
3. Sistema cria Company, User (admin), Subscription (trial, 7 dias)
4. Redireciona para dashboard do admin
5. Ao fim do trial, `CheckSubscription` bloqueia e redireciona para escolha de plano

### 7.2 Criacao e atribuicao de treinamento

1. Admin/Instrutor cria treinamento com titulo, descricao, URL de video, duracao
2. Sistema detecta provider (YouTube/Vimeo) pela URL
3. Opcionalmente cria quiz com perguntas e alternativas
4. Admin atribui treinamento a grupo(s) via `training_assignments`
5. Funcionarios do grupo veem o treinamento no dashboard

### 7.3 Funcionario assiste treinamento

1. Employee ve treinamentos pendentes no dashboard
2. Clica para assistir — pagina com player embed
3. JavaScript monitora progresso via YouTube/Vimeo Player API
4. A cada 10%, AJAX POST `/api/training-progress`
5. Ao atingir 90%, botao "Marcar como concluido" aparece
6. Se `has_quiz`, redireciona para quiz apos marcar concluido
7. Quiz aprovado + concluido = botao "Gerar Certificado"

### 7.4 Pagamento e assinatura

1. Trial expira (ou admin escolhe plano antes)
2. Admin escolhe plano e metodo de pagamento
3. `AsaasService` cria customer e subscription no Asaas
4. Webhook confirma pagamento, atualiza status para `active`
5. Cobranças recorrentes automaticas via Asaas
6. Pagamento atrasado: status `past_due`, aviso exibido, periodo de carencia
7. Apos carencia: `expired`, acesso bloqueado

### 7.5 Verificacao de certificado

1. Qualquer pessoa acessa `/certificate/verify`
2. Digita codigo do certificado
3. Sistema busca por `certificate_code` (sem scope de company)
4. Exibe dados publicos: nome, treinamento, data, empresa

---

## 8. Regras de Negocio Adicionais

### Quiz

- Employee pode refazer o quiz ilimitadamente ate passar.
- Apenas a tentativa mais recente e considerada.
- Sem cooldown entre tentativas.

### Trial e Plano

- Durante o trial, a subscription e criada com `plan_id` do plano Basic (plano padrao).
- O plano Basic define os limites durante o trial.
- Ao escolher um plano pago, o `plan_id` e atualizado.

### Carencia de Pagamento

- Quando pagamento atrasa: status muda para `past_due`.
- Periodo de carencia: 7 dias apos vencimento.
- Durante carencia: sistema exibe banner de aviso, mas permite acesso normal.
- Apos 7 dias: status muda para `expired`, acesso bloqueado pelo middleware.

### Permissoes Instructor vs Admin

- Instructor pode criar e editar apenas seus proprios treinamentos (`created_by`).
- Somente Admin pode atribuir treinamentos a grupos (`training_assignments`).
- Instructor NAO tem acesso a atribuicao de treinamentos.
- TrainingPolicy valida ownership via `created_by` para instructors.

### Slug da Empresa

- O `slug` e gerado automaticamente a partir do nome da empresa no cadastro.
- Usado internamente como identificador legivel (ex: URLs de verificacao de certificado).
- Nao e usado para roteamento de tenant (acesso e via login unico).

### Rotas do Dashboard

- `GET /dashboard` e um unico `DashboardController` que detecta o role do usuario logado e renderiza a view correspondente (`admin/dashboard`, `instructor/dashboard`, `employee/dashboard`).
- Super admin e redirecionado para `/super/dashboard`.

### Exportacao de Relatorios

- Exportacao limitada a 1000 registros por vez para evitar timeout em shared hosting.
- Para empresas com mais registros, usar filtros para reduzir o dataset.

---

## 9. Email

### Configuracao

- Driver: SMTP da Hostgator (ou servico externo como Mailtrap/Mailgun se disponivel).
- Envio sincrono (sem filas). Configurado via `.env` (`MAIL_MAILER=smtp`).
- Template de emails via Blade (Laravel Notifications).

### Emails enviados

- **Boas-vindas:** Ao cadastrar empresa, email para o admin com instrucoes.
- **Reset de senha:** Fluxo padrao do Breeze (forgot-password, reset-password).
- **Trial expirando:** 2 dias antes do fim do trial, email de aviso.
- **Pagamento confirmado:** Apos webhook de pagamento confirmado.
- **Pagamento atrasado:** Apos webhook de pagamento atrasado.

### Rotas de Auth (complemento Breeze)

```
GET  /forgot-password           Formulario esqueci minha senha
POST /forgot-password           Enviar email de reset
GET  /reset-password/{token}    Formulario de nova senha
POST /reset-password            Processar nova senha
```

---

## 10. Rotas Completas

```
# Publicas
GET  /                          Landing page
GET  /register                  Cadastro de empresa
POST /register                  Processar cadastro
GET  /certificate/verify        Verificacao publica
POST /asaas/webhook             Webhook do Asaas

# Auth (Breeze)
GET  /login
POST /login
POST /logout
GET  /forgot-password
POST /forgot-password
GET  /reset-password/{token}
POST /reset-password

# Admin (auth + role:admin + subscription)
GET  /dashboard
CRUD /users
CRUD /groups
CRUD /trainings
CRUD /training-assignments
GET  /certificates
GET  /reports
GET  /subscription

# Instructor (auth + role:instructor + subscription)
GET  /dashboard
CRUD /trainings (apenas os seus)

# Employee (auth + role:employee + subscription)
GET  /dashboard
GET  /trainings/{id}            Assistir
POST /trainings/{id}/complete   Marcar concluido
POST /trainings/{id}/quiz       Submeter quiz
GET  /certificates              Meus certificados
GET  /certificates/{id}/download

# API (AJAX)
POST /api/training-progress

# Super Admin (auth + role:super_admin)
GET  /super/dashboard
CRUD /super/companies
GET  /super/subscriptions
GET  /super/payments
CRUD /super/plans
```

---

## 11. UI e Frontend

### Layout

Sidebar + topbar. Cores da empresa injetadas via CSS variables pelo middleware `InjectCompanyTheme`. Logo da empresa na sidebar e certificados.

### Componentes Blade

```
components/
├── layout/app.blade.php        Layout principal
├── layout/guest.blade.php      Layout login/register
├── ui/card.blade.php           Card dashboard
├── ui/table.blade.php          Tabela paginada
├── ui/modal.blade.php          Modal confirmacao
├── ui/alert.blade.php          Alertas
├── ui/video-player.blade.php   Embed com tracking JS
├── forms/input.blade.php
├── forms/select.blade.php
├── forms/button.blade.php
```

### Dashboard Admin

Cards: total funcionarios, treinamentos criados, concluidos, pendentes.
Grafico de conclusao (ultimos 30 dias) com Chart.js via CDN.

### Dashboard Employee

Secoes: "Treinamentos Pendentes" e "Concluidos".
Cards com titulo, progresso (%), botao de assistir.
Area de certificados para download.

### Relatorios

- Conclusao de treinamentos: filtros por grupo/treinamento/periodo/status. Export PDF e Excel.
- Certificados emitidos: filtros por periodo/treinamento.
- Excel via maatwebsite/excel.

### Certificado PDF

Landscape. Logo da empresa, titulo, nome funcionario, treinamento, carga horaria, data, empresa, codigo verificacao, rodape com URL de verificacao.

---

## 12. Seguranca

- CSRF em todos os formularios (padrao Laravel)
- Bcrypt para senhas (Breeze padrao)
- Mass assignment via `$fillable`
- Form Requests para validacao de input
- Sanitizacao de URLs de video (validar YouTube/Vimeo)
- Upload de logo: validar tipo (jpg/png/svg), max 2MB
- Webhook Asaas: token de autenticacao armazenado em `ASAAS_WEBHOOK_TOKEN` no `.env`, validado via header `asaas-access-token` em cada request. Rota excluida do CSRF middleware. Retorna 200 mesmo para tokens invalidos (evita retries do Asaas). Processamento idempotente
- Rate limiting: login (5/min), progresso video (30/min), webhook
- Policies para autorizacao de acoes especificas

---

## 13. Performance (Shared Hosting)

- Tudo sincrono, sem filas/Redis/workers
- Cache de metricas do dashboard por 5 min via `Cache::remember()` com key por company_id
- Eager loading em todos os controllers (`with()`)
- Paginacao em todas as listagens (15 itens)
- CSS/JS compilado e commitado (sem necessidade de npm na Hostgator)
- Chart.js via CDN
- Indexes nos campos mais consultados
- `select()` especifico em queries de relatorio
- Soft deletes em companies, users e trainings

---

## 14. Pacotes Laravel

| Pacote | Uso |
|---|---|
| laravel/breeze | Autenticacao |
| barryvdh/laravel-dompdf | Geracao de PDF |
| maatwebsite/excel | Exportacao Excel |
| guzzlehttp/guzzle | Chamadas HTTP para Asaas API |
