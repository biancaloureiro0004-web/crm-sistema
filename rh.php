<?php
/**
 * rh.php — Módulo de RH (restrito a Admin)
 * Documentos de funcionários e recibos de vencimento
 */

require_once __DIR__ . '/config.php';

session_start([
    'cookie_httponly' => true,
    'cookie_secure'   => (APP_ENV === 'production'),
    'cookie_samesite' => 'Strict',
]);

requireAuth();
requireAdmin();

$pdo       = getDB();
$pageTitle = 'Recursos Humanos';

// Funcionário selecionado (por query string ou POST)
$funcIdParam = (int) ($_GET['func_id'] ?? $_POST['func_id'] ?? 0);

// ─── Processamento POST ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $acao = $_POST['acao'] ?? '';

    // ── Upload de documento de funcionário ──────────────────
    if ($acao === 'upload_doc_funcionario') {
        $funcId    = (int) ($_POST['func_id'] ?? 0);
        $nomeDoc   = trim($_POST['nome_documento'] ?? '');

        if (!$funcId || empty($nomeDoc)) {
            $_SESSION['flash_error'] = 'Funcionário e nome do documento são obrigatórios.';
        } else {
            try {
                $caminho = uploadFicheiro($_FILES['ficheiro'], 'documentos_funcionarios');
                $stmt = $pdo->prepare(
                    'INSERT INTO documentos_funcionarios (utilizador_id, nome_documento, caminho_ficheiro)
                     VALUES (:uid, :nome, :caminho)'
                );
                $stmt->execute([':uid' => $funcId, ':nome' => $nomeDoc, ':caminho' => $caminho]);
                $_SESSION['flash_success'] = 'Documento carregado com sucesso.';
            } catch (RuntimeException $e) {
                $_SESSION['flash_error'] = $e->getMessage();
            }
        }
        redirect('rh.php?func_id=' . $funcId);
    }

    // ── Emitir recibo de vencimento ──────────────────────────
    if ($acao === 'emitir_recibo') {
        $funcId       = (int) ($_POST['func_id']        ?? 0);
        $mesRef       = trim($_POST['mes_referencia']    ?? '');
        $valorPago    = (float) ($_POST['valor_pago']   ?? 0);

        if (!$funcId || empty($mesRef) || $valorPago <= 0) {
            $_SESSION['flash_error'] = 'Todos os campos do recibo são obrigatórios.';
        } elseif (!preg_match('/^\d{4}-\d{2}$/', $mesRef)) {
            $_SESSION['flash_error'] = 'Formato do mês de referência inválido (AAAA-MM).';
        } else {
            $caminhoPdf = null;
            if (!empty($_FILES['ficheiro_pdf']['name'])) {
                try {
                    $caminhoPdf = uploadFicheiro($_FILES['ficheiro_pdf'], 'recibos');
                } catch (RuntimeException $e) {
                    $_SESSION['flash_error'] = $e->getMessage();
                    redirect('rh.php?func_id=' . $funcId);
                }
            }

            // Verifica duplicado (mesmo funcionário e mês)
            $chk = $pdo->prepare(
                'SELECT id FROM recibos_vencimento WHERE utilizador_id=:uid AND mes_referencia=:mes LIMIT 1'
            );
            $chk->execute([':uid' => $funcId, ':mes' => $mesRef]);
            if ($chk->fetch()) {
                $_SESSION['flash_error'] = "Já existe um recibo para {$mesRef} deste funcionário.";
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO recibos_vencimento (utilizador_id, mes_referencia, valor_pago, ficheiro_pdf)
                     VALUES (:uid, :mes, :valor, :pdf)'
                );
                $stmt->execute([':uid' => $funcId, ':mes' => $mesRef, ':valor' => $valorPago, ':pdf' => $caminhoPdf]);
                $_SESSION['flash_success'] = 'Recibo de vencimento emitido com sucesso.';
            }
        }
        redirect('rh.php?func_id=' . $funcId);
    }

    // ── Apagar documento ──────────────────────────────────────
    if ($acao === 'apagar_doc') {
        $docId  = (int) ($_POST['doc_id']  ?? 0);
        $funcId = (int) ($_POST['func_id'] ?? 0);
        $stmt   = $pdo->prepare('SELECT caminho_ficheiro FROM documentos_funcionarios WHERE id=:id LIMIT 1');
        $stmt->execute([':id' => $docId]);
        $doc = $stmt->fetch();
        if ($doc) {
            @unlink(__DIR__ . '/' . $doc['caminho_ficheiro']);
            $pdo->prepare('DELETE FROM documentos_funcionarios WHERE id=:id')->execute([':id' => $docId]);
            $_SESSION['flash_success'] = 'Documento removido.';
        }
        redirect('rh.php?func_id=' . $funcId);
    }
}

// ─── Leitura ──────────────────────────────────────────────────
$stmtFunc = $pdo->query(
    "SELECT id, nome, cargo, estado, salario_base FROM utilizadores ORDER BY nome"
);
$funcionarios = $stmtFunc->fetchAll();

$funcSelecionado  = null;
$documentosFunc   = [];
$recibos          = [];

if ($funcIdParam > 0) {
    $stmtF = $pdo->prepare('SELECT * FROM utilizadores WHERE id=:id LIMIT 1');
    $stmtF->execute([':id' => $funcIdParam]);
    $funcSelecionado = $stmtF->fetch();

    if ($funcSelecionado) {
        $stmtDocs = $pdo->prepare(
            'SELECT * FROM documentos_funcionarios WHERE utilizador_id=:uid ORDER BY data_upload DESC'
        );
        $stmtDocs->execute([':uid' => $funcIdParam]);
        $documentosFunc = $stmtDocs->fetchAll();

        $stmtRec = $pdo->prepare(
            'SELECT * FROM recibos_vencimento WHERE utilizador_id=:uid ORDER BY mes_referencia DESC'
        );
        $stmtRec->execute([':uid' => $funcIdParam]);
        $recibos = $stmtRec->fetchAll();
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="flex gap-6">

    <!-- ── Lista de funcionários (sidebar) ─────────────────── -->
    <div class="w-64 flex-shrink-0">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 overflow-hidden shadow-sm">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-slate-800">
                <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wide">Funcionários</p>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-slate-800">
                <?php foreach ($funcionarios as $f): ?>
                <a href="rh.php?func_id=<?= $f['id'] ?>"
                   class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-slate-800/50 transition <?= $funcIdParam === $f['id'] ? 'bg-brand-50 dark:bg-brand-950/30' : '' ?>">
                    <div class="w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center text-brand-700 dark:text-brand-400 font-semibold text-sm flex-shrink-0">
                        <?= strtoupper(substr($f['nome'], 0, 1)) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-slate-100 truncate <?= $funcIdParam === $f['id'] ? 'text-brand-700 dark:text-brand-400' : '' ?>">
                            <?= h($f['nome']) ?>
                        </p>
                        <p class="text-xs text-gray-400 dark:text-slate-500 truncate"><?= h($f['cargo'] ?: '—') ?></p>
                    </div>
                    <?php if ($f['estado'] === 'inativo'): ?>
                    <span class="text-xs text-red-400 font-medium">Inativo</span>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ── Painel de detalhe ────────────────────────────────── -->
    <div class="flex-1">
        <?php if (!$funcSelecionado): ?>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 py-20 text-center text-gray-400 dark:text-slate-500 shadow-sm">
            <i data-lucide="folder-open" class="w-12 h-12 mx-auto mb-3 text-gray-200 dark:text-slate-800"></i>
            <p class="text-sm">Seleciona um funcionário para ver os documentos e recibos.</p>
        </div>
        <?php else: ?>

        <!-- Cabeçalho do funcionário -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 px-6 py-5 flex items-center justify-between mb-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center text-brand-700 dark:text-brand-400 font-bold text-xl">
                    <?= strtoupper(substr($funcSelecionado['nome'], 0, 1)) ?>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-900 dark:text-slate-100 text-lg"><?= h($funcSelecionado['nome']) ?></h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400"><?= h($funcSelecionado['cargo'] ?: 'Sem cargo') ?>
                        · Salário: <strong class="text-gray-900 dark:text-slate-200"><?= number_format($funcSelecionado['salario_base'], 2, ',', '.') ?> €</strong>
                    </p>
                </div>
            </div>
            <a href="funcionarios.php" class="text-sm text-brand-600 dark:text-brand-400 hover:underline">← Gerir equipa</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            <!-- ── Documentos do funcionário ─────────────────── -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 dark:text-slate-100">Documentos</h3>
                    <span class="text-xs text-gray-400 dark:text-slate-500"><?= count($documentosFunc) ?> ficheiro(s)</span>
                </div>

                <!-- Upload form -->
                <form method="POST" action="rh.php" enctype="multipart/form-data" class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 bg-gray-50 dark:bg-slate-900/50">
                    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                    <input type="hidden" name="acao"    value="upload_doc_funcionario">
                    <input type="hidden" name="func_id" value="<?= $funcSelecionado['id'] ?>">
                    <div class="flex gap-2">
                        <input type="text" name="nome_documento" placeholder="Nome do documento" required
                               class="flex-1 px-3 py-1.5 text-sm bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg text-gray-900 dark:text-slate-100 placeholder-gray-400 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                        <input type="file" name="ficheiro" accept=".pdf,.jpg,.png" required
                               class="text-xs text-gray-500 dark:text-slate-400 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-brand-50 dark:file:bg-brand-950/50 file:text-brand-700 dark:file:text-brand-400">
                        <button type="submit" class="px-3 py-1.5 bg-brand-600 dark:bg-brand-700 text-white text-xs font-medium rounded-lg hover:bg-brand-700 dark:hover:bg-brand-600 transition">
                            Upload
                        </button>
                    </div>
                </form>

                <!-- Lista de documentos -->
                <div class="divide-y divide-gray-50 dark:divide-slate-800 max-h-64 overflow-y-auto">
                    <?php if (empty($documentosFunc)): ?>
                        <p class="px-6 py-6 text-center text-sm text-gray-400 dark:text-slate-500">Sem documentos.</p>
                    <?php else: foreach ($documentosFunc as $doc): ?>
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-800/40 transition">
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                            <i data-lucide="file-text" class="w-4 h-4 text-gray-400 dark:text-slate-500 flex-shrink-0"></i>
                            <div class="min-w-0">
                                <a href="<?= APP_URL ?>/<?= h($doc['caminho_ficheiro']) ?>" target="_blank"
                                   class="text-sm text-brand-600 dark:text-brand-400 hover:underline truncate block"><?= h($doc['nome_documento']) ?></a>
                                <p class="text-xs text-gray-400 dark:text-slate-500"><?= date('d/m/Y', strtotime($doc['data_upload'])) ?></p>
                            </div>
                        </div>
                        <form method="POST" action="rh.php" onsubmit="return confirm('Remover este documento?')">
                            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                            <input type="hidden" name="acao"    value="apagar_doc">
                            <input type="hidden" name="doc_id"  value="<?= $doc['id'] ?>">
                            <input type="hidden" name="func_id" value="<?= $funcSelecionado['id'] ?>">
                            <button type="submit" class="p-1 text-gray-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-400 transition">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- ── Recibos de Vencimento ──────────────────────── -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800">
                    <h3 class="font-semibold text-gray-900 dark:text-slate-100">Recibos de Vencimento</h3>
                </div>

                <!-- Formulário de emissão -->
                <form method="POST" action="rh.php" enctype="multipart/form-data" class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 bg-gray-50 dark:bg-slate-900/50 space-y-3">
                    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                    <input type="hidden" name="acao"    value="emitir_recibo">
                    <input type="hidden" name="func_id" value="<?= $funcSelecionado['id'] ?>">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Mês Referência</label>
                            <input type="month" name="mes_referencia" value="<?= date('Y-m') ?>" required
                                   class="w-full px-3 py-1.5 text-sm bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg text-gray-900 dark:text-slate-100 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Valor Pago (€)</label>
                            <input type="number" name="valor_pago" step="0.01" min="0.01"
                                   value="<?= $funcSelecionado['salario_base'] ?>" required
                                   class="w-full px-3 py-1.5 text-sm bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg text-gray-900 dark:text-slate-100 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">PDF do Recibo (opcional)</label>
                        <input type="file" name="ficheiro_pdf" accept=".pdf"
                               class="w-full text-xs text-gray-500 dark:text-slate-400 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-brand-50 dark:file:bg-brand-950/50 file:text-brand-700 dark:file:text-brand-400">
                    </div>
                    <button type="submit" class="w-full py-1.5 bg-brand-600 dark:bg-brand-700 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 dark:hover:bg-brand-600 transition shadow-sm">
                        Emitir Recibo
                    </button>
                </form>

                <!-- Histórico de recibos -->
                <div class="divide-y divide-gray-50 dark:divide-slate-800 max-h-64 overflow-y-auto">
                    <?php if (empty($recibos)): ?>
                        <p class="px-6 py-6 text-center text-sm text-gray-400 dark:text-slate-500">Sem recibos emitidos.</p>
                    <?php else: foreach ($recibos as $r): ?>
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-800/40 transition">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-slate-100"><?= h($r['mes_referencia']) ?></p>
                            <p class="text-xs text-gray-400 dark:text-slate-500">
                                <?= number_format($r['valor_pago'], 2, ',', '.') ?> €
                                · <?= date('d/m/Y', strtotime($r['data_emissao'])) ?>
                            </p>
                        </div>
                        <?php if ($r['ficheiro_pdf']): ?>
                        <a href="<?= APP_URL ?>/<?= h($r['ficheiro_pdf']) ?>" target="_blank"
                           class="text-xs text-brand-600 dark:text-brand-400 hover:underline flex items-center gap-1 font-medium">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i> PDF
                        </a>
                        <?php else: ?>
                        <span class="text-xs text-gray-400 dark:text-slate-600">Sem PDF</span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>

        <?php endif; // funcSelecionado ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>