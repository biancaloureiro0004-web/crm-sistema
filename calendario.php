<?php
/**
 * calendario.php — Calendário de tarefas (individual e equipa)
 * v2: Dark mode completo + cookie_samesite Lax (fix sessão no localhost)
 *
 * NOTA: A tabela na BD chama-se 'calendario_tarefas' — todas as queries
 * apontam explicitamente para esse nome.
 */

require_once __DIR__ . '/config.php';

session_start([
    'cookie_httponly' => true,
    'cookie_secure'   => (APP_ENV === 'production'),
    'cookie_samesite' => 'Lax',   // 'Strict' quebra sessão após redirect no localhost
]);

requireAuth();

$pdo       = getDB();
$userId    = (int) $_SESSION['user_id'];
$isAdmin   = hasRole('admin');
$pageTitle = 'Calendário';

// ─── Processamento POST ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF robusto: regenera token se ausente (após login faz unset)
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        die('Token CSRF inválido. Por favor recarrega a página.');
    }

    $acao = $_POST['acao'] ?? '';

    // ── Criar tarefa ─────────────────────────────────────────
    if ($acao === 'criar_tarefa') {
        $titulo     = trim($_POST['titulo']    ?? '');
        $descricao  = trim($_POST['descricao'] ?? '');
        $dataHora   = trim($_POST['data_hora'] ?? '');
        $tipo       = ($_POST['tipo'] ?? '') === 'equipa' ? 'equipa' : 'individual';
        $atribuidoA = $tipo === 'individual'
            ? (((int)($_POST['atribuido_a'] ?? 0)) ?: $userId)
            : null;

        if (empty($titulo) || empty($dataHora)) {
            $_SESSION['flash_error'] = 'Título e data são obrigatórios.';
        } else {
            // Tabela: calendario_tarefas (nome confirmado no crm_servicos.sql)
            $stmt = $pdo->prepare(
                'INSERT INTO calendario_tarefas
                    (titulo, descricao, data_hora, tipo, criado_por, atribuido_a)
                 VALUES
                    (:t, :d, :dh, :tipo, :uid, :atrib)'
            );
            $stmt->execute([
                ':t'     => $titulo,
                ':d'     => $descricao,
                ':dh'    => $dataHora,
                ':tipo'  => $tipo,
                ':uid'   => $userId,
                ':atrib' => $atribuidoA,
            ]);
            $_SESSION['flash_success'] = 'Tarefa criada com sucesso.';
        }
        redirect('calendario.php');
    }

    // ── Apagar tarefa ────────────────────────────────────────
    if ($acao === 'apagar_tarefa') {
        $tarefaId = (int) ($_POST['tarefa_id'] ?? 0);
        // Funcionário só pode apagar as suas; admin apaga qualquer uma
        $where = $isAdmin
            ? 'id = :id'
            : 'id = :id AND criado_por = :uid';
        $pms = $isAdmin
            ? [':id' => $tarefaId]
            : [':id' => $tarefaId, ':uid' => $userId];

        $pdo->prepare("DELETE FROM calendario_tarefas WHERE {$where}")->execute($pms);
        $_SESSION['flash_success'] = 'Tarefa removida.';
        redirect('calendario.php');
    }
}

// ─── Leitura: mês visualizado ─────────────────────────────────
$mes = max(1, min(12, (int)($_GET['mes'] ?? date('n'))));
$ano = (int)($_GET['ano'] ?? date('Y'));

$dataInicio = sprintf('%04d-%02d-01 00:00:00', $ano, $mes);
$dataFim    = date('Y-m-t 23:59:59', strtotime(sprintf('%04d-%02d-01', $ano, $mes)));

// Query diferenciada por role
// Tabela: calendario_tarefas
if ($isAdmin) {
    // Admin vê TUDO no mês
    $stmtT = $pdo->prepare(
        "SELECT ct.*,
                u.nome  AS criador_nome,
                ua.nome AS atribuido_nome
           FROM calendario_tarefas ct
           JOIN utilizadores u   ON u.id  = ct.criado_por
      LEFT JOIN utilizadores ua  ON ua.id = ct.atribuido_a
          WHERE ct.data_hora BETWEEN :inicio AND :fim
          ORDER BY ct.data_hora ASC"
    );
    $stmtT->execute([':inicio' => $dataInicio, ':fim' => $dataFim]);
} else {
    // Funcionário vê: tarefas de equipa + as suas individuais (criadas ou atribuídas)
    $stmtT = $pdo->prepare(
        "SELECT ct.*,
                u.nome  AS criador_nome,
                ua.nome AS atribuido_nome
           FROM calendario_tarefas ct
           JOIN utilizadores u   ON u.id  = ct.criado_por
      LEFT JOIN utilizadores ua  ON ua.id = ct.atribuido_a
          WHERE ct.data_hora BETWEEN :inicio AND :fim
            AND (
                ct.tipo = 'equipa'
                OR ct.atribuido_a = :uid
                OR ct.criado_por  = :uid2
            )
          ORDER BY ct.data_hora ASC"
    );
    $stmtT->execute([
        ':inicio' => $dataInicio,
        ':fim'    => $dataFim,
        ':uid'    => $userId,
        ':uid2'   => $userId,
    ]);
}
$tarefas = $stmtT->fetchAll();

// Indexa por dia para a grelha do calendário
$tarefasPorDia = [];
foreach ($tarefas as $t) {
    $tarefasPorDia[(int)date('j', strtotime($t['data_hora']))][] = $t;
}

// Funcionários para o select do modal (admin cria tarefas para outros)
$funcionarios = [];
if ($isAdmin) {
    $funcionarios = $pdo->query(
        "SELECT id, nome FROM utilizadores WHERE estado = 'ativo' ORDER BY nome"
    )->fetchAll();
}

// Navegação entre meses
$mesAnterior = $mes === 1  ? ['m' => 12, 'a' => $ano - 1] : ['m' => $mes - 1, 'a' => $ano];
$mesSeguinte = $mes === 12 ? ['m' => 1,  'a' => $ano + 1] : ['m' => $mes + 1, 'a' => $ano];

$mesesPt = [
    1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',
    5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',
    9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro',
];
$nomeMes     = $mesesPt[$mes];
$diasNoMes   = (int)date('t', mktime(0, 0, 0, $mes, 1, $ano));
$primeiroDia = (int)date('N', mktime(0, 0, 0, $mes, 1, $ano)); // 1=Seg…7=Dom

include __DIR__ . '/includes/header.php';
?>

<div class="flex flex-col lg:flex-row gap-6">

    <!-- ════════════════════════════════════════════════════
         GRELHA DO CALENDÁRIO
    ════════════════════════════════════════════════════ -->
    <div class="flex-1 min-w-0">

        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">

            <!-- Navegação de mês -->
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <a href="?mes=<?= $mesAnterior['m'] ?>&ano=<?= $mesAnterior['a'] ?>"
                   class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition text-gray-500 dark:text-gray-400">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </a>
                <h2 class="font-semibold text-gray-900 dark:text-white capitalize">
                    <?= $nomeMes ?> <?= $ano ?>
                </h2>
                <a href="?mes=<?= $mesSeguinte['m'] ?>&ano=<?= $mesSeguinte['a'] ?>"
                   class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition text-gray-500 dark:text-gray-400">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            </div>

            <!-- Cabeçalho: dias da semana -->
            <div class="grid grid-cols-7 text-center text-xs font-medium
                        text-gray-500 dark:text-gray-400
                        bg-gray-50 dark:bg-gray-800/60
                        border-b border-gray-100 dark:border-gray-800">
                <?php foreach (['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'] as $d): ?>
                <div class="py-2"><?= $d ?></div>
                <?php endforeach; ?>
            </div>

            <!-- Grelha de dias -->
            <div class="grid grid-cols-7 divide-x divide-gray-100 dark:divide-gray-800">

                <?php
                // Células em branco antes do dia 1
                for ($i = 1; $i < $primeiroDia; $i++):
                ?>
                <div class="min-h-[88px] bg-gray-50/60 dark:bg-gray-900/40
                            border-b border-gray-100 dark:border-gray-800"></div>
                <?php endfor; ?>

                <?php for ($dia = 1; $dia <= $diasNoMes; $dia++):
                    $hoje       = ($dia === (int)date('j') && $mes === (int)date('n') && $ano === (int)date('Y'));
                    $temTarefas = !empty($tarefasPorDia[$dia]);
                    $col        = ($primeiroDia + $dia - 2) % 7;  // 0=Seg…6=Dom
                    $fimSemana  = $col >= 5;
                ?>
                <div class="min-h-[88px] p-1.5 border-b border-gray-100 dark:border-gray-800 cursor-pointer
                            <?= $fimSemana ? 'bg-gray-50/60 dark:bg-gray-900/40' : 'bg-white dark:bg-gray-900' ?>
                            <?= $hoje ? 'ring-1 ring-inset ring-brand-400 dark:ring-brand-600' : '' ?>
                            hover:bg-brand-50/40 dark:hover:bg-brand-950/40 transition"
                     onclick="abrirModalTarefa(<?= $dia ?>, <?= $mes ?>, <?= $ano ?>)">

                    <!-- Número do dia -->
                    <div class="flex justify-start mb-1">
                        <?php if ($hoje): ?>
                        <span class="w-6 h-6 rounded-full bg-brand-600 text-white text-xs font-bold
                                     flex items-center justify-center leading-none">
                            <?= $dia ?>
                        </span>
                        <?php else: ?>
                        <span class="text-xs font-medium
                                     <?= $fimSemana ? 'text-gray-400 dark:text-gray-600' : 'text-gray-500 dark:text-gray-400' ?>">
                            <?= $dia ?>
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Tarefas do dia -->
                    <?php if ($temTarefas): foreach ($tarefasPorDia[$dia] as $t): ?>
                    <div class="text-xs px-1.5 py-0.5 rounded mb-0.5 truncate leading-snug
                                <?= $t['tipo'] === 'equipa'
                                    ? 'bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 font-medium'
                                    : 'bg-brand-100 dark:bg-brand-900/60 text-brand-800 dark:text-brand-200' ?>"
                         title="<?= h($t['titulo']) ?> — <?= date('H:i', strtotime($t['data_hora'])) ?>">
                        <?= date('H:i', strtotime($t['data_hora'])) ?>
                        <?= h(mb_substr($t['titulo'], 0, 16)) ?>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
                <?php endfor; ?>

                <?php
                // Células em branco no fim para completar a grelha
                $totalCelulas = $primeiroDia - 1 + $diasNoMes;
                $celulasFim   = (7 - ($totalCelulas % 7)) % 7;
                for ($i = 0; $i < $celulasFim; $i++):
                ?>
                <div class="min-h-[88px] bg-gray-50/60 dark:bg-gray-900/40
                            border-b border-gray-100 dark:border-gray-800"></div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Legenda -->
        <div class="flex items-center gap-5 mt-3 text-xs text-gray-500 dark:text-gray-400">
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-brand-300 dark:bg-brand-700"></span>
                Tarefa individual
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-amber-300 dark:bg-amber-700"></span>
                Tarefa de equipa
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full border-2 border-brand-500"></span>
                Hoje
            </span>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════
         PAINEL LATERAL: lista de tarefas do mês
    ════════════════════════════════════════════════════ -->
    <div class="w-full lg:w-72 flex-shrink-0">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">

            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800
                        flex items-center justify-between">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">
                    Tarefas — <?= $nomeMes ?>
                </h3>
                <button onclick="abrirModalTarefa()"
                        class="flex items-center gap-1 text-xs text-brand-600 dark:text-brand-400
                               hover:text-brand-700 dark:hover:text-brand-300 font-medium transition">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Adicionar
                </button>
            </div>

            <div class="divide-y divide-gray-50 dark:divide-gray-800
                        max-h-[calc(100vh-260px)] overflow-y-auto">

                <?php if (empty($tarefas)): ?>
                <p class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                    Sem tarefas em <?= $nomeMes ?>.
                </p>
                <?php else: foreach ($tarefas as $t): ?>

                <div class="px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                    <div class="flex items-start gap-3">
                        <!-- Indicador de tipo -->
                        <span class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0
                                     <?= $t['tipo'] === 'equipa'
                                         ? 'bg-amber-400 dark:bg-amber-500'
                                         : 'bg-brand-400 dark:bg-brand-500' ?>"></span>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">
                                <?= h($t['titulo']) ?>
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                <?= date('d/m H:i', strtotime($t['data_hora'])) ?>
                                <?php if ($t['tipo'] === 'equipa'): ?>
                                · <span class="text-amber-600 dark:text-amber-400 font-medium">Equipa</span>
                                <?php elseif (!empty($t['atribuido_nome'])): ?>
                                · <span class="text-gray-500 dark:text-gray-400"><?= h($t['atribuido_nome']) ?></span>
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($t['descricao'])): ?>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                <?= h($t['descricao']) ?>
                            </p>
                            <?php endif; ?>
                        </div>

                        <!-- Botão apagar (criador ou admin) -->
                        <?php if ($isAdmin || (int)$t['criado_por'] === $userId): ?>
                        <form method="POST" action="calendario.php"
                              onsubmit="return confirm('Apagar esta tarefa?')">
                            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                            <input type="hidden" name="acao"       value="apagar_tarefa">
                            <input type="hidden" name="tarefa_id"  value="<?= $t['id'] ?>">
                            <button type="submit"
                                    class="p-1 text-gray-300 dark:text-gray-600
                                           hover:text-red-500 dark:hover:text-red-400 transition mt-0.5">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>

                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════
     MODAL: CRIAR TAREFA
════════════════════════════════════════════════════════════ -->
<div id="modal-tarefa"
     class="fixed inset-0 z-50 hidden items-center justify-center
             bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md">

        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800
                    flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 dark:text-white text-lg">Nova Tarefa</h3>
            <button onclick="fecharModalTarefa()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="calendario.php" class="px-6 py-5 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
            <input type="hidden" name="acao" value="criar_tarefa">

            <!-- Título -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Título *
                </label>
                <input type="text" name="titulo" required
                       class="w-full px-3 py-2 text-sm rounded-lg outline-none
                              border border-gray-300 dark:border-gray-700
                              bg-white dark:bg-gray-800
                              text-gray-900 dark:text-white
                              focus:ring-2 focus:ring-brand-500 focus:border-brand-500
                              placeholder-gray-400 dark:placeholder-gray-500">
            </div>

            <!-- Descrição -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Descrição
                </label>
                <textarea name="descricao" rows="2"
                          class="w-full px-3 py-2 text-sm rounded-lg outline-none resize-none
                                 border border-gray-300 dark:border-gray-700
                                 bg-white dark:bg-gray-800
                                 text-gray-900 dark:text-white
                                 focus:ring-2 focus:ring-brand-500 focus:border-brand-500
                                 placeholder-gray-400 dark:placeholder-gray-500"></textarea>
            </div>

            <!-- Data e hora -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Data e Hora *
                </label>
                <input type="datetime-local" name="data_hora" id="tarefa-data-hora" required
                       class="w-full px-3 py-2 text-sm rounded-lg outline-none
                              border border-gray-300 dark:border-gray-700
                              bg-white dark:bg-gray-800
                              text-gray-900 dark:text-white
                              focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
            </div>

            <!-- Tipo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Tipo
                </label>
                <select name="tipo" id="tarefa-tipo" onchange="toggleAtribuido(this.value)"
                        class="w-full px-3 py-2 text-sm rounded-lg outline-none
                               border border-gray-300 dark:border-gray-700
                               bg-white dark:bg-gray-800
                               text-gray-900 dark:text-white
                               focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    <option value="individual">Individual</option>
                    <option value="equipa">Equipa — visível a todos</option>
                </select>
            </div>

            <!-- Atribuição (oculto quando tipo=equipa) -->
            <div id="div-atribuido">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Atribuir a
                </label>
                <select name="atribuido_a"
                        class="w-full px-3 py-2 text-sm rounded-lg outline-none
                               border border-gray-300 dark:border-gray-700
                               bg-white dark:bg-gray-800
                               text-gray-900 dark:text-white
                               focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    <option value="<?= $userId ?>">Eu mesmo</option>
                    <?php if ($isAdmin): foreach ($funcionarios as $f):
                        if ((int)$f['id'] === $userId) continue; ?>
                    <option value="<?= $f['id'] ?>"><?= h($f['nome']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>

            <!-- Acções -->
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-brand-600 hover:bg-brand-700 text-white
                               text-sm font-medium py-2.5 rounded-lg transition">
                    Guardar Tarefa
                </button>
                <button type="button" onclick="fecharModalTarefa()"
                        class="px-5 py-2.5 text-sm rounded-lg transition
                               text-gray-600 dark:text-gray-300
                               border border-gray-300 dark:border-gray-700
                               hover:bg-gray-50 dark:hover:bg-gray-800">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Modal de criação de tarefa ────────────────────────────────
function abrirModalTarefa(dia, mes, ano) {
    const modal = document.getElementById('modal-tarefa');
    if (dia && mes && ano) {
        const pad = n => String(n).padStart(2, '0');
        document.getElementById('tarefa-data-hora').value =
            `${ano}-${pad(mes)}-${pad(dia)}T09:00`;
    }
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    lucide.createIcons();
}

function fecharModalTarefa() {
    const m = document.getElementById('modal-tarefa');
    m.classList.add('hidden');
    m.classList.remove('flex');
}

function toggleAtribuido(tipo) {
    document.getElementById('div-atribuido').style.display =
        tipo === 'equipa' ? 'none' : 'block';
}

document.getElementById('modal-tarefa').addEventListener('click', function(e) {
    if (e.target === this) fecharModalTarefa();
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
