<?php
/**
 * dashboard.php — Painel principal
 * v2: Dark mode completo + cookie_samesite Lax
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
$pageTitle = 'Dashboard';

// ─── Métricas ─────────────────────────────────────────────────
$whereClause = $isAdmin ? '' : 'WHERE l.criado_por = :uid';
$params      = $isAdmin ? [] : [':uid' => $userId];

$stmtStats = $pdo->prepare(
    "SELECT COUNT(*) AS total,
            SUM(CASE WHEN estado='Ganho'            THEN 1 ELSE 0 END) AS ganho,
            SUM(CASE WHEN estado='Perdido'          THEN 1 ELSE 0 END) AS perdido,
            SUM(CASE WHEN estado='Nova Lead'        THEN 1 ELSE 0 END) AS nova,
            SUM(CASE WHEN estado='Proposta Enviada' THEN 1 ELSE 0 END) AS proposta
       FROM leads l {$whereClause}"
);
$stmtStats->execute($params);
$stats = $stmtStats->fetch();

$taxaConversao = $stats['total'] > 0
    ? round(($stats['ganho'] / $stats['total']) * 100, 1)
    : 0;

// ─── Performance (Admin) ──────────────────────────────────────
$performance = [];
if ($isAdmin) {
    $performance = $pdo->query(
        "SELECT u.nome,
                COUNT(l.id)                                            AS total,
                SUM(CASE WHEN l.estado='Ganho'   THEN 1 ELSE 0 END)   AS ganho,
                SUM(CASE WHEN l.estado='Perdido' THEN 1 ELSE 0 END)   AS perdido
           FROM utilizadores u
      LEFT JOIN leads l ON l.criado_por = u.id
          WHERE u.estado = 'ativo'
       GROUP BY u.id, u.nome
       ORDER BY ganho DESC"
    )->fetchAll();
}

// ─── Próximas tarefas ─────────────────────────────────────────
$stmtTarefas = $pdo->prepare(
    "SELECT ct.*, u.nome AS criador_nome
       FROM calendario_tarefas ct
       JOIN utilizadores u ON u.id = ct.criado_por
      WHERE (ct.tipo='equipa' OR ct.atribuido_a=:uid " . ($isAdmin ? "OR 1=1" : "") . ")
        AND ct.data_hora >= NOW()
        AND ct.data_hora <= DATE_ADD(NOW(), INTERVAL 7 DAY)
      ORDER BY ct.data_hora ASC LIMIT 10"
);
$stmtTarefas->execute([':uid' => $userId]);
$tarefas = $stmtTarefas->fetchAll();

// ─── Últimas leads ────────────────────────────────────────────
$stmtLeads = $pdo->prepare(
    "SELECT l.*, u.nome AS criador
       FROM leads l JOIN utilizadores u ON u.id = l.criado_por
     " . ($isAdmin ? '' : 'WHERE l.criado_por = :uid') . "
     ORDER BY l.created_at DESC LIMIT 5"
);
$stmtLeads->execute($isAdmin ? [] : [':uid' => $userId]);
$ultimasLeads = $stmtLeads->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<!-- ── Cartões de métricas ────────────────────────────────── -->
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
<?php
$cards = [
    ['label'=>'Total Leads',       'value'=>$stats['total'],    'light'=>'bg-blue-50 text-blue-700',   'dark'=>'dark:bg-blue-950 dark:text-blue-300',   'icon'=>'users'],
    ['label'=>'Leads Ganhas',      'value'=>$stats['ganho'],    'light'=>'bg-green-50 text-green-700', 'dark'=>'dark:bg-green-950 dark:text-green-300', 'icon'=>'check-circle'],
    ['label'=>'Leads Perdidas',    'value'=>$stats['perdido'],  'light'=>'bg-red-50 text-red-700',     'dark'=>'dark:bg-red-950 dark:text-red-300',     'icon'=>'x-circle'],
    ['label'=>'Propostas',         'value'=>$stats['proposta'], 'light'=>'bg-purple-50 text-purple-700','dark'=>'dark:bg-purple-950 dark:text-purple-300','icon'=>'send'],
    ['label'=>'Taxa Conversão',    'value'=>$taxaConversao.'%', 'light'=>'bg-amber-50 text-amber-700', 'dark'=>'dark:bg-amber-950 dark:text-amber-300', 'icon'=>'trending-up'],
];
foreach ($cards as $c):
?>
<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800
            p-5 flex flex-col gap-3 hover:shadow-sm transition">
    <div class="flex items-center justify-between">
        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
            <?= $c['label'] ?>
        </span>
        <span class="p-1.5 rounded-lg <?= $c['light'] ?> <?= $c['dark'] ?>">
            <i data-lucide="<?= $c['icon'] ?>" class="w-4 h-4"></i>
        </span>
    </div>
    <p class="text-3xl font-bold text-gray-900 dark:text-white"><?= $c['value'] ?></p>
</div>
<?php endforeach; ?>
</div>

<!-- ── Linha principal ───────────────────────────────────── -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    <!-- Últimas Leads -->
    <div class="lg:col-span-2 bg-white dark:bg-gray-900
                rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800
                    flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 dark:text-white">Últimas Leads</h2>
            <a href="<?= APP_URL ?>/leads.php"
               class="text-xs text-brand-600 dark:text-brand-400 hover:underline font-medium">
               Ver todas →
            </a>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-800">
            <?php if (empty($ultimasLeads)): ?>
            <p class="px-6 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                Ainda não existem leads registadas.
            </p>
            <?php else: foreach ($ultimasLeads as $lead):
                $bc = match($lead['estado']) {
                    'Nova Lead'         => 'badge-nova',
                    'Contacto Efetuado' => 'badge-contacto',
                    'Proposta Enviada'  => 'badge-proposta',
                    'Ganho'             => 'badge-ganho',
                    'Perdido'           => 'badge-perdido',
                    default             => 'bg-gray-100 text-gray-600',
                };
            ?>
            <div class="px-6 py-3 flex items-center justify-between
                        hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                <div>
                    <a href="<?= APP_URL ?>/leads.php"
                       class="text-sm font-medium text-gray-900 dark:text-white
                              hover:text-brand-600 dark:hover:text-brand-400">
                        <?= h($lead['nome_cliente']) ?>
                    </a>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                        <?= h($lead['criador']) ?> · <?= date('d/m/Y', strtotime($lead['created_at'])) ?>
                    </p>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full <?= $bc ?>">
                    <?= h($lead['estado']) ?>
                </span>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Próximas Tarefas -->
    <div class="bg-white dark:bg-gray-900
                rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800
                    flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 dark:text-white">Próximas Tarefas</h2>
            <a href="<?= APP_URL ?>/calendario.php"
               class="text-xs text-brand-600 dark:text-brand-400 hover:underline font-medium">
               Calendário →
            </a>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-800">
            <?php if (empty($tarefas)): ?>
            <p class="px-6 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                Sem tarefas para os próximos 7 dias.
            </p>
            <?php else: foreach ($tarefas as $t): ?>
            <div class="px-5 py-3">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg flex-shrink-0 mt-0.5 flex items-center justify-center
                                <?= $t['tipo']==='equipa'
                                    ? 'bg-amber-50 dark:bg-amber-900/40'
                                    : 'bg-brand-50 dark:bg-brand-900/40' ?>">
                        <i data-lucide="<?= $t['tipo']==='equipa' ? 'users' : 'user' ?>"
                           class="w-4 h-4 <?= $t['tipo']==='equipa'
                               ? 'text-amber-600 dark:text-amber-400'
                               : 'text-brand-600 dark:text-brand-400' ?>"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">
                            <?= h($t['titulo']) ?>
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            <?= date('d/m H:i', strtotime($t['data_hora'])) ?>
                        </p>
                        <?php if ($t['tipo']==='equipa'): ?>
                        <span class="inline-block mt-1 text-xs font-medium px-1.5 py-0.5 rounded badge-equipa">
                            Equipa
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<!-- ── Performance da equipa (Admin) ────────────────────── -->
<?php if ($isAdmin && !empty($performance)): ?>
<div class="bg-white dark:bg-gray-900
            rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
        <h2 class="font-semibold text-gray-900 dark:text-white">Performance da Equipa</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60
                           text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide
                           border-b border-gray-100 dark:border-gray-800">
                    <th class="px-6 py-3 text-left">Funcionário</th>
                    <th class="px-6 py-3 text-right">Total</th>
                    <th class="px-6 py-3 text-right">Ganhas</th>
                    <th class="px-6 py-3 text-right">Perdidas</th>
                    <th class="px-6 py-3 text-right">Taxa</th>
                    <th class="px-6 py-3 w-36">Progresso</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <?php foreach ($performance as $p):
                $taxa = $p['total'] > 0 ? round(($p['ganho'] / $p['total']) * 100) : 0;
            ?>
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full
                                    bg-brand-100 dark:bg-brand-900
                                    text-brand-700 dark:text-brand-300
                                    flex items-center justify-center font-semibold text-xs flex-shrink-0">
                            <?= strtoupper(substr($p['nome'], 0, 1)) ?>
                        </div>
                        <?= h($p['nome']) ?>
                    </div>
                </td>
                <td class="px-6 py-3 text-right text-gray-600 dark:text-gray-300"><?= $p['total'] ?></td>
                <td class="px-6 py-3 text-right text-green-600 dark:text-green-400 font-medium"><?= $p['ganho'] ?></td>
                <td class="px-6 py-3 text-right text-red-500 dark:text-red-400"><?= $p['perdido'] ?></td>
                <td class="px-6 py-3 text-right font-semibold
                           <?= $taxa >= 50
                               ? 'text-green-600 dark:text-green-400'
                               : 'text-amber-600 dark:text-amber-400' ?>">
                    <?= $taxa ?>%
                </td>
                <td class="px-6 py-3">
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                        <div class="bg-brand-500 h-1.5 rounded-full transition-all"
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

<?php include __DIR__ . '/includes/footer.php'; ?>
