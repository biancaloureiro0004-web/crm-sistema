<?php
/**
 * relatorios.php — Relatórios e Estatísticas de Performance
 * v2: Isolamento Admin vs Funcionário + dark mode + sem colunas inexistentes
 *
 * Regras de acesso:
 *   Admin      → vê TODAS as leads do sistema no período selecionado
 *   Funcionário → vê APENAS as leads que ele próprio criou (criado_por = userId)
 *
 * Colunas disponíveis na tabela 'leads' (confirmado em crm_servicos.sql):
 *   id, nome_cliente, email, telefone, morada, nif, cc,
 *   estado (enum), criado_por, created_at, updated_at
 *   NÃO existem: canal, valor — removidas as queries que as usavam
 */

require_once __DIR__ . '/config.php';

session_start([
    'cookie_httponly' => true,
    'cookie_secure'   => (APP_ENV === 'production'),
    'cookie_samesite' => 'Lax',
]);

requireAuth();

$pdo       = getDB();
$userId    = (int) $_SESSION['user_id'];
$isAdmin   = hasRole('admin');
$pageTitle = 'Relatórios';

// ─── Filtro de período ────────────────────────────────────────
$mes = max(1, min(12, (int)($_GET['mes'] ?? date('n'))));
$ano = (int)($_GET['ano'] ?? date('Y'));

$dataInicio = sprintf('%04d-%02d-01 00:00:00', $ano, $mes);
$dataFim    = date('Y-m-t 23:59:59', strtotime(sprintf('%04d-%02d-01', $ano, $mes)));

// ─── Cláusula WHERE consoante o role ─────────────────────────
//
//  Admin      → filtra apenas por período (vê tudo)
//  Funcionário → filtra por período E pelo seu ID (isolamento total)
//
if ($isAdmin) {
    $whereBase   = 'WHERE l.created_at BETWEEN :inicio AND :fim';
    $paramsBase  = [':inicio' => $dataInicio, ':fim' => $dataFim];
} else {
    $whereBase   = 'WHERE l.criado_por = :uid AND l.created_at BETWEEN :inicio AND :fim';
    $paramsBase  = [':uid' => $userId, ':inicio' => $dataInicio, ':fim' => $dataFim];
}

// ─── 1. Resumo geral (estados) ────────────────────────────────
$stmtResumo = $pdo->prepare(
    "SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN estado = 'Ganho'            THEN 1 ELSE 0 END) AS ganho,
        SUM(CASE WHEN estado = 'Perdido'          THEN 1 ELSE 0 END) AS perdido,
        SUM(CASE WHEN estado = 'Proposta Enviada' THEN 1 ELSE 0 END) AS proposta,
        SUM(CASE WHEN estado = 'Nova Lead'        THEN 1 ELSE 0 END) AS nova,
        SUM(CASE WHEN estado = 'Contacto Efetuado' THEN 1 ELSE 0 END) AS contacto
     FROM leads l
     {$whereBase}"
);
$stmtResumo->execute($paramsBase);
$resumo = $stmtResumo->fetch();

$taxaConversao = $resumo['total'] > 0
    ? round(($resumo['ganho'] / $resumo['total']) * 100, 1)
    : 0;

// ─── 2. Performance por funcionário (apenas Admin) ────────────
$performanceFuncs = [];
if ($isAdmin) {
    // Junta todos os utilizadores com as suas leads no período
    $stmtPerf = $pdo->prepare(
        "SELECT
            u.nome,
            u.cargo,
            COUNT(l.id)                                                AS total,
            SUM(CASE WHEN l.estado = 'Ganho'   THEN 1 ELSE 0 END)     AS ganho,
            SUM(CASE WHEN l.estado = 'Perdido' THEN 1 ELSE 0 END)     AS perdido,
            SUM(CASE WHEN l.estado = 'Proposta Enviada' THEN 1 ELSE 0 END) AS proposta
         FROM utilizadores u
    LEFT JOIN leads l ON l.criado_por = u.id
                      AND l.created_at BETWEEN :inicio AND :fim
        WHERE u.estado = 'ativo'
        GROUP BY u.id, u.nome, u.cargo
        ORDER BY ganho DESC, total DESC"
    );
    $stmtPerf->execute([':inicio' => $dataInicio, ':fim' => $dataFim]);
    $performanceFuncs = $stmtPerf->fetchAll();
}

// ─── 3. Evolução mensal do último ano (sparkline) ─────────────
//  Conta leads criadas por mês nos últimos 12 meses
$whereEvolucao = $isAdmin
    ? ''
    : 'AND l.criado_por = :uid';

$stmtEvolucao = $pdo->prepare(
    "SELECT
        DATE_FORMAT(l.created_at, '%Y-%m') AS mes_ano,
        COUNT(*)                            AS total,
        SUM(CASE WHEN estado = 'Ganho' THEN 1 ELSE 0 END) AS ganho
     FROM leads l
     WHERE l.created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
     {$whereEvolucao}
     GROUP BY mes_ano
     ORDER BY mes_ano ASC"
);
$paramsEvolucao = $isAdmin ? [] : [':uid' => $userId];
$stmtEvolucao->execute($paramsEvolucao);
$evolucao = $stmtEvolucao->fetchAll();

// ─── 4. Top clientes (leads ganhas) no período ───────────────
$stmtTopClientes = $pdo->prepare(
    "SELECT l.nome_cliente, l.email, u.nome AS responsavel,
            l.created_at
       FROM leads l
       JOIN utilizadores u ON u.id = l.criado_por
      {$whereBase}
        AND l.estado = 'Ganho'
      ORDER BY l.created_at DESC
      LIMIT 8"
);
$stmtTopClientes->execute($paramsBase);
$topClientes = $stmtTopClientes->fetchAll();

// ─── Meses para o filtro ──────────────────────────────────────
$mesesPt = [
    1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',
    5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',
    9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro',
];

include __DIR__ . '/includes/header.php';
?>

<!-- ════════════════════════════════════════════════════════════
     BARRA DE FILTROS
════════════════════════════════════════════════════════════ -->
<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800
            p-4 mb-6 flex flex-wrap items-center justify-between gap-4">

    <div class="flex items-center gap-2">
        <i data-lucide="filter" class="w-4 h-4 text-gray-400 dark:text-gray-500"></i>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Período de Análise:</span>
        <?php if (!$isAdmin): ?>
        <span class="text-xs bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300
                     px-2 py-0.5 rounded-full font-medium">
            Os teus dados
        </span>
        <?php endif; ?>
    </div>

    <form method="GET" action="relatorios.php" class="flex items-center gap-2 flex-wrap">
        <select name="mes"
                class="px-3 py-1.5 text-sm rounded-lg outline-none
                       border border-gray-300 dark:border-gray-700
                       bg-white dark:bg-gray-800
                       text-gray-700 dark:text-gray-200
                       focus:ring-2 focus:ring-brand-500">
            <?php foreach ($mesesPt as $num => $nome): ?>
            <option value="<?= $num ?>" <?= $num === $mes ? 'selected' : '' ?>><?= $nome ?></option>
            <?php endforeach; ?>
        </select>

        <select name="ano"
                class="px-3 py-1.5 text-sm rounded-lg outline-none
                       border border-gray-300 dark:border-gray-700
                       bg-white dark:bg-gray-800
                       text-gray-700 dark:text-gray-200
                       focus:ring-2 focus:ring-brand-500">
            <?php for ($i = date('Y'); $i >= date('Y') - 3; $i--): ?>
            <option value="<?= $i ?>" <?= $i === $ano ? 'selected' : '' ?>><?= $i ?></option>
            <?php endfor; ?>
        </select>

        <button type="submit"
                class="bg-brand-600 hover:bg-brand-700 text-white
                       text-sm font-medium px-4 py-1.5 rounded-lg transition">
            Filtrar
        </button>

        <?php if ($mes !== (int)date('n') || $ano !== (int)date('Y')): ?>
        <a href="relatorios.php"
           class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition">
            Mês atual ↩
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- ════════════════════════════════════════════════════════════
     CARTÕES DE MÉTRICAS
════════════════════════════════════════════════════════════ -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">
            Total de Leads
        </p>
        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-bold text-gray-900 dark:text-white"><?= $resumo['total'] ?></span>
            <span class="text-xs text-gray-400 dark:text-gray-500">no período</span>
        </div>
        <div class="mt-2 flex gap-2 text-xs">
            <span class="text-blue-500"><?= $resumo['nova'] ?> novas</span>
            <span class="text-gray-300 dark:text-gray-600">·</span>
            <span class="text-yellow-500"><?= $resumo['contacto'] ?> contacto</span>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
        <p class="text-xs font-semibold text-green-500 uppercase tracking-wider mb-2">
            Taxa de Conversão
        </p>
        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-bold text-green-600 dark:text-green-400">
                <?= $taxaConversao ?>%
            </span>
        </div>
        <div class="mt-2 text-xs text-gray-400 dark:text-gray-500">
            <?= $resumo['ganho'] ?> ganhas · <?= $resumo['perdido'] ?> perdidas
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
        <p class="text-xs font-semibold text-purple-500 uppercase tracking-wider mb-2">
            Em Proposta
        </p>
        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                <?= $resumo['proposta'] ?>
            </span>
            <span class="text-xs text-gray-400 dark:text-gray-500">leads</span>
        </div>
        <div class="mt-2 text-xs text-gray-400 dark:text-gray-500">
            <?= $resumo['total'] > 0 ? round(($resumo['proposta'] / $resumo['total']) * 100) : 0 ?>% do total
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
        <p class="text-xs font-semibold text-red-500 uppercase tracking-wider mb-2">
            Perdidas
        </p>
        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-bold text-red-500 dark:text-red-400">
                <?= $resumo['perdido'] ?>
            </span>
            <span class="text-xs text-gray-400 dark:text-gray-500">leads</span>
        </div>
        <div class="mt-2 text-xs text-gray-400 dark:text-gray-500">
            Taxa de perda:
            <?= $resumo['total'] > 0 ? round(($resumo['perdido'] / $resumo['total']) * 100) : 0 ?>%
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════
     LINHA PRINCIPAL: Funil + Evolução Mensal
════════════════════════════════════════════════════════════ -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    <!-- Funil de estados -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
        <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-5 flex items-center gap-2">
            <i data-lucide="bar-chart-3" class="w-4 h-4 text-brand-500"></i>
            Funil de Estados — <?= $mesesPt[$mes] ?> <?= $ano ?>
        </h3>

        <?php if ($resumo['total'] === 0): ?>
        <div class="text-center py-10">
            <i data-lucide="inbox" class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2"></i>
            <p class="text-sm text-gray-400 dark:text-gray-500">
                Sem leads em <?= $mesesPt[$mes] ?> <?= $ano ?>.
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-600 mt-1">
                Experimenta selecionar outro período no filtro acima.
            </p>
        </div>
        <?php else: ?>
        <div class="space-y-4">
            <?php
            $estadosFunil = [
                ['label' => 'Nova Lead',         'key' => 'nova',      'cor' => 'bg-blue-500',   'corText' => 'text-blue-600 dark:text-blue-400'],
                ['label' => 'Contacto Efetuado', 'key' => 'contacto',  'cor' => 'bg-yellow-500', 'corText' => 'text-yellow-600 dark:text-yellow-400'],
                ['label' => 'Proposta Enviada',  'key' => 'proposta',  'cor' => 'bg-purple-500', 'corText' => 'text-purple-600 dark:text-purple-400'],
                ['label' => 'Ganho',             'key' => 'ganho',     'cor' => 'bg-green-500',  'corText' => 'text-green-600 dark:text-green-400'],
                ['label' => 'Perdido',           'key' => 'perdido',   'cor' => 'bg-red-500',    'corText' => 'text-red-500 dark:text-red-400'],
            ];
            foreach ($estadosFunil as $est):
                $qtd  = (int)($resumo[$est['key']] ?? 0);
                $pct  = $resumo['total'] > 0 ? round(($qtd / $resumo['total']) * 100) : 0;
            ?>
            <div>
                <div class="flex justify-between text-xs mb-1.5">
                    <span class="font-medium text-gray-600 dark:text-gray-300"><?= $est['label'] ?></span>
                    <span class="font-bold <?= $est['corText'] ?>">
                        <?= $qtd ?>
                        <span class="font-normal text-gray-400 dark:text-gray-500">(<?= $pct ?>%)</span>
                    </span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2.5">
                    <div class="<?= $est['cor'] ?> h-2.5 rounded-full transition-all duration-700"
                         style="width: <?= $pct ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Evolução mensal (últimos 12 meses) -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
        <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-5 flex items-center gap-2">
            <i data-lucide="trending-up" class="w-4 h-4 text-green-500"></i>
            Evolução — Últimos 12 Meses
            <?php if (!$isAdmin): ?>
            <span class="text-xs font-normal text-gray-400 dark:text-gray-500">(as tuas leads)</span>
            <?php endif; ?>
        </h3>

        <?php if (empty($evolucao)): ?>
        <div class="text-center py-10">
            <i data-lucide="bar-chart-2" class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2"></i>
            <p class="text-sm text-gray-400 dark:text-gray-500">Sem dados históricos.</p>
        </div>
        <?php else:
            $maxTotal = max(array_column($evolucao, 'total')) ?: 1;
        ?>
        <div class="space-y-2.5">
            <?php foreach ($evolucao as $ev):
                $pctTotal = round(($ev['total'] / $maxTotal) * 100);
                $pctGanho = $ev['total'] > 0 ? round(($ev['ganho'] / $ev['total']) * 100) : 0;
                [$eAno, $eMes] = explode('-', $ev['mes_ano']);
                $nomeM = $mesesPt[(int)$eMes] ?? $ev['mes_ano'];
            ?>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500 dark:text-gray-400 w-20 flex-shrink-0 text-right">
                    <?= substr($nomeM, 0, 3) ?> <?= $eAno ?>
                </span>
                <div class="flex-1 relative h-6 bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden">
                    <!-- Barra total (cinzento) -->
                    <div class="absolute inset-y-0 left-0 bg-brand-200 dark:bg-brand-900 rounded-lg transition-all duration-500"
                         style="width: <?= $pctTotal ?>%"></div>
                    <!-- Barra ganhas (verde) -->
                    <div class="absolute inset-y-0 left-0 bg-green-400 dark:bg-green-600 rounded-lg transition-all duration-700"
                         style="width: <?= round(($ev['ganho'] / $maxTotal) * 100) ?>%"></div>
                    <span class="absolute inset-0 flex items-center px-2 text-xs font-semibold
                                 text-gray-700 dark:text-gray-200">
                        <?= $ev['total'] ?> leads · <span class="text-green-700 dark:text-green-300 ml-1"><?= $ev['ganho'] ?> ganhas</span>
                    </span>
                </div>
                <span class="text-xs font-medium w-10 text-right
                             <?= $pctGanho >= 50 ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' ?>">
                    <?= $pctGanho ?>%
                </span>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="flex items-center gap-4 mt-4 text-xs text-gray-400 dark:text-gray-500">
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-2 rounded bg-brand-200 dark:bg-brand-900"></span> Total
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-2 rounded bg-green-400 dark:bg-green-600"></span> Ganhas
            </span>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════
     LINHA INFERIOR: Performance da equipa (Admin) + Top clientes
════════════════════════════════════════════════════════════ -->
<div class="grid grid-cols-1 <?= $isAdmin ? 'lg:grid-cols-2' : '' ?> gap-6">

    <!-- Performance por funcionário (APENAS ADMIN) -->
    <?php if ($isAdmin && !empty($performanceFuncs)): ?>
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm flex items-center gap-2">
                <i data-lucide="users" class="w-4 h-4 text-brand-500"></i>
                Performance da Equipa — <?= $mesesPt[$mes] ?> <?= $ano ?>
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/60 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        <th class="px-5 py-3 text-left">Colaborador</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Ganhas</th>
                        <th class="px-4 py-3 text-right">Perdidas</th>
                        <th class="px-4 py-3 text-right">Taxa</th>
                        <th class="px-4 py-3 w-28">Progresso</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                <?php foreach ($performanceFuncs as $p):
                    $taxa = $p['total'] > 0 ? round(($p['ganho'] / $p['total']) * 100) : 0;
                ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-brand-100 dark:bg-brand-900
                                        flex items-center justify-center
                                        text-brand-700 dark:text-brand-300
                                        font-semibold text-xs flex-shrink-0">
                                <?= strtoupper(substr($p['nome'], 0, 1)) ?>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white leading-tight">
                                    <?= h($p['nome']) ?>
                                </p>
                                <?php if ($p['cargo']): ?>
                                <p class="text-xs text-gray-400 dark:text-gray-500"><?= h($p['cargo']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300"><?= $p['total'] ?></td>
                    <td class="px-4 py-3 text-right font-semibold text-green-600 dark:text-green-400"><?= $p['ganho'] ?></td>
                    <td class="px-4 py-3 text-right text-red-500 dark:text-red-400"><?= $p['perdido'] ?></td>
                    <td class="px-4 py-3 text-right font-bold
                               <?= $taxa >= 50 ? 'text-green-600 dark:text-green-400' : 'text-amber-500 dark:text-amber-400' ?>">
                        <?= $taxa ?>%
                    </td>
                    <td class="px-4 py-3">
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                            <div class="<?= $taxa >= 50 ? 'bg-green-500' : 'bg-amber-400' ?> h-1.5 rounded-full transition-all"
                                 style="width: <?= $taxa ?>%"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Top clientes ganhos no período -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
                Negócios Fechados — <?= $mesesPt[$mes] ?> <?= $ano ?>
                <?php if (!$isAdmin): ?>
                <span class="text-xs font-normal text-gray-400">(os teus)</span>
                <?php endif; ?>
            </h3>
        </div>

        <?php if (empty($topClientes)): ?>
        <div class="px-6 py-10 text-center">
            <i data-lucide="trophy" class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2"></i>
            <p class="text-sm text-gray-400 dark:text-gray-500">
                Ainda sem negócios fechados neste período.
            </p>
        </div>
        <?php else: ?>
        <div class="divide-y divide-gray-50 dark:divide-gray-800">
            <?php foreach ($topClientes as $c): ?>
            <div class="px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            <?= h($c['nome_cliente']) ?>
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            <?= h($c['email'] ?: '—') ?>
                            <?php if ($isAdmin): ?>
                            · <span class="text-brand-500 dark:text-brand-400"><?= h($c['responsavel']) ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0 ml-4">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full badge-ganho">
                            Ganho
                        </span>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            <?= date('d/m/Y', strtotime($c['created_at'])) ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
