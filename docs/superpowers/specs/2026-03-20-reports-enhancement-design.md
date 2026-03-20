# Design Spec: Melhorias na Tela de Relatórios

**Data:** 20 de março de 2026
**Status:** Aguardando aprovação
**Autor:** Claude Code

---

## 1. Visão Geral

A tela de relatórios será melhorada para oferecer:
- **Filtros em tempo real** sem recarregar a página (busca AJAX com debounce)
- **Métricas globais** que refletem o total de registros, não apenas dados paginados
- **Sistema de abas** com relatórios temáticos (Geral, Por Grupo, Por Instrutor, Por Período)
- **Visualizações gráficas** para melhor análise comparativa

---

## 2. Estrutura de Layout

### 2.1 Seções Principais

```
┌─────────────────────────────────────────┐
│  HEADER (fixo)                          │
│  Título "Relatórios" + Exportar PDF/XLS │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│  FILTROS STICKY (fica fixo ao rolar)    │
│  [Treinamento] [Grupo] [Status]         │
│  [Data início] [Data fim] [Limpar]      │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│  KPIs GLOBAIS (4 cards)                 │
│  [36 Registros] [12 Concluídos]         │
│  [3 Pendentes] [89% Prog Médio]         │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│  ABAS | Geral | Por Grupo | Por Instrutor│ Por Período
├─────────────────────────────────────────┤
│  CONTEÚDO DA ABA (gráficos + tabela)    │
│                                         │
│  [Gráfico/Tabela conforme aba]          │
└─────────────────────────────────────────┘
```

### 2.2 Comportamento Sticky dos Filtros

- Filtros permanecem visíveis ao rolar a página (position: sticky)
- Todos os filtros aplicam-se simultaneamente
- Mudança em qualquer filtro dispara busca automática com debounce de 400ms
- Indicador visual quando filtros estão ativos (badge "Filtros aplicados")

---

## 3. Métricas Globais (KPIs)

Os 4 cards devem mostrar **totais da base completa**, não apenas da página atual:

| Card | Métrica | Cálculo | Exemplo |
|------|---------|---------|---------|
| 1 | Registros totais | COUNT(todas as trainings_views) | 150 |
| 2 | Concluídos (total) | COUNT(trainings_views WHERE completed_at IS NOT NULL) | 87 |
| 3 | Pendentes (total) | COUNT(trainings_views WHERE completed_at IS NULL) | 63 |
| 4 | Progresso médio | AVG(progress_percent) | 58% |

**Comportamento:** As métricas se atualizam quando filtros são aplicados (refletem totais filtrados)

---

## 4. Sistema de Abas

### 4.1 Aba "Geral"

**Objetivo:** Lista completa de registros como está atualmente (melhorada)

**Conteúdo:**
- Tabela paginada com colunas:
  - Funcionário (com avatar)
  - Treinamento
  - Progresso (barra)
  - Status (badge verde/amarelo)
  - Data de Conclusão

**Filtros aplicáveis:**
- Treinamento, Grupo, Status, Data início/fim

**Interação:**
- Clique em linha pode abrir detalhes do progresso (modal ou página)

---

### 4.2 Aba "Por Grupo"

**Objetivo:** Comparar performance entre grupos

**Conteúdo:**

1. **Gráfico de Barras Horizontal**
   - Eixo Y: Nomes dos grupos
   - Eixo X: % de conclusão
   - Cores: Verde (100%), Azul (50-99%), Amarelo (<50%)

2. **Tabela Resumida**
   - Colunas: Grupo | Total de Pessoas | Concluídos | Pendentes | % Médio Progresso
   - Ordenável por clique no header
   - Cada linha clicável para expandir colaboradores do grupo

**Filtros aplicáveis:**
- Treinamento, Status, Data início/fim
- (Grupo NÃO aparece, pois é o eixo principal)

**Dados esperados:**
```
Grupo A: 25 pessoas, 18 concluídos, 7 pendentes, 72% progresso
Grupo B: 15 pessoas, 12 concluídos, 3 pendentes, 81% progresso
Grupo C: 10 pessoas, 5 concluídos, 5 pendentes, 45% progresso
```

---

### 4.3 Aba "Por Instrutor"

**Objetivo:** Comparar qualidade/desempenho dos instrutores

**Conteúdo:**

1. **Gráfico de Barras Vertical**
   - Eixo X: Nomes dos instrutores
   - Eixo Y: Média de progresso dos alunos
   - Mostrar apenas instrutores ativos/com dados

2. **Tabela Resumida**
   - Colunas: Instrutor | Total de Alunos | Concluídos | % Médio Progresso | Taxa de Conclusão
   - Identificar outliers (instrutores com baixa performance)

**Filtros aplicáveis:**
- Treinamento, Grupo, Data início/fim

**Dados esperados:**
```
João Silva: 20 alunos, 16 concluídos, 78% progresso, 80% taxa
Maria Santos: 18 alunos, 15 concluídos, 85% progresso, 83% taxa
```

---

### 4.4 Aba "Por Período"

**Objetivo:** Visualizar evolução de conclusões ao longo do tempo

**Conteúdo:**

1. **Gráfico de Linha**
   - Eixo X: Períodos (por semana ou mês, conforme filtro de data)
   - Eixo Y: Quantidade de conclusões
   - Múltiplas linhas: Conclusões totais, Pendentes iniciados, Taxa de crescimento

2. **Tabela Resumida**
   - Colunas: Período | Conclusões Neste Período | Acumulado | % Crescimento
   - Mostrar tendência com seta (↑ crescimento, ↓ queda)

**Filtros aplicáveis:**
- Treinamento, Grupo, Data início/fim

**Dados esperados:**
```
Semana 1 (11-17 mar): 5 conclusões, 5 acumulado, —
Semana 2 (18-24 mar): 8 conclusões, 13 acumulado, ↑ 60%
Semana 3 (25-31 mar): 6 conclusões, 19 acumulado, ↓ 25%
```

---

## 5. Comportamento de Filtros

### 5.1 Filtros Disponíveis (Sticky Bar)

```html
[Treinamento ▼] [Grupo ▼] [Status ▼] [Data início 📅] [Data fim 📅] [Limpar]
```

- **Treinamento:** Select com opção "Todos"
- **Grupo:** Select com opção "Todos"
- **Status:** Select (Todos, Concluído, Pendente)
- **Data início/fim:** Date inputs
- **Limpar:** Link que reseta todos os filtros

### 5.2 Lógica AJAX

1. Usuário muda qualquer filtro
2. Sistema aguarda 400ms (debounce)
3. Se houver mudança, faz requisição GET para `/admin/reports/filter`
4. Servidor retorna:
   - Dados para tabela/gráfico da aba atual
   - KPIs atualizados (globais)
5. DOM é atualizado sem recarregar página
6. URL muda (usando History API) para manter filtros ao compartilhar/recarregar

### 5.3 Estados Visuais

- **Carregando:** Skeleton loaders na tabela/gráfico
- **Sem dados:** Mensagem "Nenhum registro encontrado com os filtros aplicados"
- **Filtros ativos:** Badge "2 filtros ativos" perto do botão Limpar

---

## 6. Tecnologia e Implementação

### 6.1 Frontend

- **Framework:** Alpine.js (já usado no projeto)
- **Gráficos:** Chart.js com plugin para responsividade
- **AJAX:** Fetch API com debounce
- **Styling:** Tailwind CSS (manter padrão do projeto)

### 6.2 Backend (Laravel)

**Controllers:** `ReportsController`
- `index()` - retorna view inicial
- `filter()` - endpoint AJAX que retorna JSON com dados filtrados

**Models/Queries:**
- `TrainingView` model com métodos para:
  - `getGlobalStats()` - KPIs totais
  - `getGroupAnalysis()` - dados por grupo
  - `getInstructorAnalysis()` - dados por instrutor
  - `getPeriodAnalysis()` - dados por período

**Database:**
- Usar queries otimizadas com cache para estatísticas pesadas

### 6.3 Componentes Reutilizáveis

- `<x-reports.filter-sticky>` - barra de filtros
- `<x-reports.kpi-card>` - card de métrica
- `<x-reports.chart>` - wrapper genérico para Chart.js
- `<x-reports.tab-panel>` - conteúdo de cada aba

---

## 7. Critérios de Sucesso

✅ Filtros funcionam em tempo real sem recarregar página
✅ KPIs mostram totais globais (não apenas paginados)
✅ Abas alternam conteúdo sem perder filtros aplicados
✅ Gráficos são responsivos (mobile, tablet, desktop)
✅ Performance: filtro retorna em <500ms mesmo com muitos dados
✅ URL reflete filtros aplicados (shareable)
✅ Exportação PDF/Excel funcionam com filtros aplicados

---

## 8. Detalhes de UX

- **Mobile:** Abas em scroll horizontal, filtros colapsáveis em small screens
- **Acessibilidade:** Aria-labels nos gráficos, teclado navegável
- **Feedback:** Toast/notificação quando filtro é aplicado
- **Persistência:** Filtros salvos em sessionStorage (reseta ao fechar aba)

---

## 9. Próximas Fases (Future)

- Agendamento automático de relatórios por email
- Filtros salvos (favoritos)
- Comparação entre períodos (year-over-year)
- Previsão de conclusões com ML

---

**Pronto para implementação? Ou gostaria de ajustes no spec?**
