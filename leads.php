<?php
/**
 * leads.php — Gestão de Leads
 * v3: Detalhes em modal grande, dark mode, CSRF robusto, samesite=Lax
 */

require_once __DIR__ . '/config.php';

session_start([
    'cookie_httponly' => true,
    'cookie_secure'   => (APP_ENV === 'production'),
    'cookie_samesite' => 'Lax',
]);

requireAuth();

$pdo     = getDB();
$userId  = (int) $_SESSION['user_id'];
$isAdmin = hasRole('admin');

// ─── Helpers de email ─────────────────────────────────────────
function enviarEmailProposta(string $dest, string $nome, string $caminho, string $nomeFich): bool {
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return _enviarPHPMailer($dest, $nome, $caminho, $nomeFich);
    }
    return _enviarNativo($dest, $nome, $caminho);
}

function _enviarPHPMailer(string $dest, string $nome, string $caminho, string $nomeFich): bool {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP(); $mail->Host = MAIL_HOST; $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME; $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION === 'ssl'
            ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MAIL_PORT; $mail->CharSet = 'UTF-8';
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($dest, $nome);
        $mail->addAttachment(__DIR__ . '/' . $caminho, $nomeFich);
        $mail->isHTML(true);
        $mail->Subject = 'Proposta de Serviço — ' . APP_NAME;
        $mail->Body = _emailBody($nome);
        $mail->send();
        return true;
    } catch (\Exception $e) { error_log('PHPMailer: ' . $e->getMessage()); return false; }
}

function _enviarNativo(string $dest, string $nome, string $caminho): bool {
    $b = md5(uniqid('', true));
    $h = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\nMIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"{$b}\"\r\n";
    $c = "--{$b}\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n" . _emailBody($nome) . "\r\n\r\n";
    $fp = __DIR__ . '/' . $caminho;
    if (file_exists($fp)) {
        $c .= "--{$b}\r\nContent-Type: application/pdf; name=\"proposta.pdf\"\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: attachment; filename=\"proposta.pdf\"\r\n\r\n" . chunk_split(base64_encode(file_get_contents($fp))) . "\r\n";
    }
    $c .= "--{$b}--";
    return mail($dest, '=?UTF-8?B?' . base64_encode('Proposta — ' . APP_NAME) . '?=', $c, $h);
}

function _emailBody(string $nome): string {
    return "<html><body style='font-family:Arial,sans-serif;color:#333;max-width:600px'>
        <div style='background:#3a4eed;padding:24px;border-radius:8px 8px 0 0;text-align:center'>
            <h2 style='color:#fff;margin:0'>" . APP_NAME . "</h2></div>
        <div style='padding:32px;background:#f9f9f9'>
            <p>Olá <strong>{$nome}</strong>,</p>
            <p>Segue em anexo a nossa proposta. Estamos disponíveis para qualquer esclarecimento.</p>
            <p style='margin-top:24px'>Com os melhores cumprimentos,<br><strong>" . APP_NAME . "</strong></p>
        </div></body></html>";
}

// ─── Processamento POST ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF: regenera se estiver vazio (após login que faz unset)
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        die('Token CSRF inválido. Por favor recarrega a página.');
    }

    $acao = $_POST['acao'] ?? '';

    // ── Criar / Editar Lead ──────────────────────────────────
    if ($acao === 'salvar_lead') {
        $leadId   = (int) ($_POST['lead_id'] ?? 0);
        $nome     = trim($_POST['nome_cliente'] ?? '');
        $email    = trim($_POST['email']        ?? '');
        $telefone = trim($_POST['telefone']      ?? '');
        $morada   = trim($_POST['morada']        ?? '');
        $nif      = trim($_POST['nif']           ?? '');
        $cc       = trim($_POST['cc']            ?? '');
        $estado   = $_POST['estado'] ?? 'Nova Lead';

        if (!in_array($estado, ['Nova Lead','Contacto Efetuado','Proposta Enviada','Ganho','Perdido'], true)) {
            $estado = 'Nova Lead';
        }

        if (empty($nome)) {
            $_SESSION['flash_error'] = 'O nome do cliente é obrigatório.';
        } else {
            if ($leadId > 0) {
                $chk = $pdo->prepare('SELECT criado_por FROM leads WHERE id = :id');
                $chk->execute([':id' => $leadId]);
                $ex  = $chk->fetch();
                if ($ex && ($isAdmin || (int)$ex['criado_por'] === $userId)) {
                    $pdo->prepare('UPDATE leads SET nome_cliente=:n,email=:e,telefone=:t,morada=:m,nif=:nif,cc=:cc,estado=:s WHERE id=:id')
                        ->execute([':n'=>$nome,':e'=>$email,':t'=>$telefone,':m'=>$morada,':nif'=>$nif,':cc'=>$cc,':s'=>$estado,':id'=>$leadId]);
                    $_SESSION['flash_success'] = 'Lead atualizada com sucesso.';
                } else {
                    $_SESSION['flash_error'] = 'Sem permissão para editar esta lead.';
                }
            } else {
                if ($userId <= 0) { redirect('login.php'); }
                $pdo->prepare('INSERT INTO leads (nome_cliente,email,telefone,morada,nif,cc,estado,criado_por) VALUES (:n,:e,:t,:m,:nif,:cc,:s,:uid)')
                    ->execute([':n'=>$nome,':e'=>$email,':t'=>$telefone,':m'=>$morada,':nif'=>$nif,':cc'=>$cc,':s'=>$estado,':uid'=>$userId]);
                $_SESSION['flash_success'] = 'Lead criada com sucesso.';
            }
        }
        redirect('leads.php');
    }

    // ── Upload de Proposta + Email ────────────────────────────
    if ($acao === 'upload_proposta') {
        $leadId = (int) ($_POST['lead_id'] ?? 0);
        if ($leadId <= 0) { $_SESSION['flash_error'] = 'Lead inválida.'; redirect('leads.php'); }

        $lead = $pdo->prepare('SELECT * FROM leads WHERE id=:id LIMIT 1');
        $lead->execute([':id' => $leadId]);
        $lead = $lead->fetch();
        if (!$lead) { $_SESSION['flash_error'] = 'Lead não encontrada.'; redirect('leads.php'); }

        try {
            $caminho = uploadFicheiro($_FILES['ficheiro_proposta'], 'propostas');

            $pdo->prepare('INSERT INTO documentos_leads (lead_id,nome_documento,caminho_ficheiro,enviado_por) VALUES (:lid,:nome,:c,:uid)')
                ->execute([':lid'=>$leadId,':nome'=>'Proposta — '.date('d/m/Y'),':c'=>$caminho,':uid'=>$userId]);

            $pdo->prepare("UPDATE leads SET estado='Proposta Enviada' WHERE id=:id")->execute([':id'=>$leadId]);

            $pdo->prepare('INSERT INTO historico_contacto (lead_id,utilizador_id,descricao) VALUES (:lid,:uid,:d)')
                ->execute([':lid'=>$leadId,':uid'=>$userId,':d'=>'Proposta enviada em '.date('d/m/Y H:i')]);

            $emailOk = false;
            if (!empty($lead['email'])) {
                $emailOk = enviarEmailProposta($lead['email'], $lead['nome_cliente'], $caminho, basename($caminho));
            }
            $_SESSION['flash_success'] = 'Proposta carregada' . ($emailOk ? ' e email enviado.' : '. (Verifique configurações SMTP para envio de email.)');
        } catch (RuntimeException $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }
        redirect('leads.php');
    }

    // ── Adicionar Nota ───────────────────────────────────────
    if ($acao === 'adicionar_nota') {
        $leadId = (int) ($_POST['lead_id'] ?? 0);
        $nota   = trim($_POST['nota'] ?? '');
        if ($leadId > 0 && !empty($nota)) {
            $pdo->prepare('INSERT INTO historico_contacto (lead_id,utilizador_id,descricao) VALUES (:lid,:uid,:d)')
                ->execute([':lid'=>$leadId,':uid'=>$userId,':d'=>$nota]);
            $_SESSION['flash_success'] = 'Nota adicionada.';
        }
        // Devolve JSON para o modal AJAX
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true]);
            exit;
        }
        redirect('leads.php');
    }
}

// ─── Leitura: listagem + filtros ──────────────────────────────
$filtroEstado = $_GET['estado'] ?? '';
$filtroBusca  = trim($_GET['busca'] ?? '');

$where = $isAdmin ? '1=1' : 'l.criado_por = :uid';
$bindP = $isAdmin ? [] : [':uid' => $userId];

if ($filtroEstado) { $where .= ' AND l.estado = :estado'; $bindP[':estado'] = $filtroEstado; }
if ($filtroBusca)  {
    $where .= ' AND (l.nome_cliente LIKE :b OR l.email LIKE :b2 OR l.telefone LIKE :b3)';
    $bindP[':b'] = $bindP[':b2'] = $bindP[':b3'] = "%{$filtroBusca}%";
}

$stmtLeads = $pdo->prepare("SELECT l.*, u.nome AS criador FROM leads l JOIN utilizadores u ON u.id=l.criado_por WHERE {$where} ORDER BY l.created_at DESC");
$stmtLeads->execute($bindP);
$leads = $stmtLeads->fetchAll();

$pageTitle = 'Leads';
include __DIR__ . '/includes/header.php';
?>

<!-- ── Barra de acções ──────────────────────────────────────── -->
<div class="flex flex-col sm:flex-row gap-4 mb-6">
    <form method="GET" action="leads.php" class="flex gap-2 flex-1">
        <input type="text" name="busca" value="<?= h($filtroBusca) ?>"
               placeholder="Pesquisar nome, email ou telefone…"
               class="flex-1 px-4 py-2 text-sm border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
        <select name="estado"
                class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
            <option value="">Todos os estados</option>
            <?php foreach (['Nova Lead','Contacto Efetuado','Proposta Enviada','Ganho','Perdido'] as $e): ?>
            <option value="<?= $e ?>" <?= $filtroEstado === $e ? 'selected' : '' ?>><?= $e ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm rounded-lg transition">Filtrar</button>
        <?php if ($filtroEstado || $filtroBusca): ?>
        <a href="leads.php" class="px-3 py-2 text-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg">✕</a>
        <?php endif; ?>
    </form>
    <button onclick="abrirModalLead()"
            class="flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition">
        <i data-lucide="plus" class="w-4 h-4"></i> Nova Lead
    </button>
</div>

<!-- ── Tabela de Leads ──────────────────────────────────────── -->
<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-200 dark:border-gray-700">
                    <th class="px-5 py-3 text-left">Cliente</th>
                    <th class="px-5 py-3 text-left hidden md:table-cell">Contacto</th>
                    <th class="px-5 py-3 text-left hidden lg:table-cell">Criada por</th>
                    <th class="px-5 py-3 text-left">Estado</th>
                    <th class="px-5 py-3 text-left hidden sm:table-cell">Data</th>
                    <th class="px-5 py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <?php if (empty($leads)): ?>
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">
                    Nenhuma lead. <a href="#" onclick="abrirModalLead();return false;" class="text-brand-600 hover:underline">Criar a primeira →</a>
                </td></tr>
            <?php else: foreach ($leads as $lead):
                $badgeClass = match($lead['estado']) {
                    'Nova Lead'         => 'badge-nova',
                    'Contacto Efetuado' => 'badge-contacto',
                    'Proposta Enviada'  => 'badge-proposta',
                    'Ganho'             => 'badge-ganho',
                    'Perdido'           => 'badge-perdido',
                    default             => 'bg-gray-100 text-gray-600',
                };
            ?>
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition">
                <td class="px-5 py-3">
                    <p class="font-medium text-gray-900 dark:text-white"><?= h($lead['nome_cliente']) ?></p>
                    <?php if ($lead['nif']): ?><p class="text-xs text-gray-400">NIF: <?= h($lead['nif']) ?></p><?php endif; ?>
                </td>
                <td class="px-5 py-3 hidden md:table-cell text-gray-500 dark:text-gray-400">
                    <?= h($lead['email'] ?: '—') ?><br>
                    <span class="text-xs"><?= h($lead['telefone'] ?: '') ?></span>
                </td>
                <td class="px-5 py-3 hidden lg:table-cell text-gray-500 dark:text-gray-400"><?= h($lead['criador']) ?></td>
                <td class="px-5 py-3">
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full <?= $badgeClass ?>"><?= h($lead['estado']) ?></span>
                </td>
                <td class="px-5 py-3 hidden sm:table-cell text-gray-400 text-xs">
                    <?= date('d/m/Y', strtotime($lead['created_at'])) ?>
                </td>
                <td class="px-5 py-3 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <!-- Botão "Ver Detalhes" → abre MODAL com AJAX -->
                        <button onclick="abrirModalDetalhe(<?= $lead['id'] ?>)"
                                class="p-1.5 text-gray-400 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-950 rounded-lg transition" title="Ver detalhe">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                        <button onclick="abrirModalLead(<?= htmlspecialchars(json_encode($lead), ENT_QUOTES) ?>)"
                                class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950 rounded-lg transition" title="Editar">
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                        </button>
                        <?php if (!in_array($lead['estado'], ['Proposta Enviada','Ganho'], true)): ?>
                        <button onclick="abrirModalProposta(<?= $lead['id'] ?>, '<?= h($lead['nome_cliente']) ?>')"
                                class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-950 rounded-lg transition" title="Enviar Proposta">
                            <i data-lucide="send" class="w-4 h-4"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════
     MODAL: CRIAR / EDITAR LEAD
════════════════════════════════════════════════════════════ -->
<div id="modal-lead" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <h3 id="modal-lead-titulo" class="font-semibold text-gray-900 dark:text-white text-lg">Nova Lead</h3>
            <button onclick="fecharModalLead()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="leads.php" class="px-6 py-5 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
            <input type="hidden" name="acao"    value="salvar_lead">
            <input type="hidden" name="lead_id" id="form-lead-id" value="0">

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome do Cliente *</label>
                    <input type="text" name="nome_cliente" id="form-nome" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" name="email" id="form-email"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone</label>
                    <input type="text" name="telefone" id="form-telefone"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIF</label>
                    <input type="text" name="nif" id="form-nif"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CC</label>
                    <input type="text" name="cc" id="form-cc"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Morada</label>
                    <input type="text" name="morada" id="form-morada"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                    <select name="estado" id="form-estado"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
                        <?php foreach (['Nova Lead','Contacto Efetuado','Proposta Enviada','Ganho','Perdido'] as $e): ?>
                        <option value="<?= $e ?>"><?= $e ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium py-2.5 rounded-lg transition">Guardar Lead</button>
                <button type="button" onclick="fecharModalLead()" class="px-5 py-2.5 text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-lg transition">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════
     MODAL: ENVIAR PROPOSTA
════════════════════════════════════════════════════════════ -->
<div id="modal-proposta" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 dark:text-white text-lg">Enviar Proposta</h3>
            <button onclick="fecharModalProposta()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="leads.php" enctype="multipart/form-data" class="px-6 py-5">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
            <input type="hidden" name="acao"    value="upload_proposta">
            <input type="hidden" name="lead_id" id="proposta-lead-id" value="">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Para: <strong id="proposta-cliente" class="text-gray-900 dark:text-white"></strong></p>
            <div class="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl p-8 text-center hover:border-brand-400 transition mb-4">
                <i data-lucide="upload-cloud" class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3"></i>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Arrasta o PDF ou clica para selecionar</p>
                <input type="file" name="ficheiro_proposta" accept=".pdf" required
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
            </div>
            <div class="bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 rounded-lg px-4 py-3 text-xs text-amber-700 dark:text-amber-300 mb-5 flex gap-2">
                <i data-lucide="info" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
                O estado passa automaticamente para <strong>Proposta Enviada</strong> e o PDF é enviado por email.
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium py-2.5 rounded-lg transition flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i> Enviar Proposta
                </button>
                <button type="button" onclick="fecharModalProposta()" class="px-5 py-2.5 text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-lg">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════
     MODAL: DETALHE DA LEAD (carrega via AJAX, substitui scroll)
════════════════════════════════════════════════════════════ -->
<div id="modal-detalhe" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">

        <!-- Cabeçalho do modal detalhe -->
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-brand-100 dark:bg-brand-900 flex items-center justify-center text-brand-700 dark:text-brand-300 font-bold text-base" id="det-avatar">?</div>
                <div>
                    <h3 id="det-nome" class="font-semibold text-gray-900 dark:text-white text-base">—</h3>
                    <span id="det-badge" class="text-xs font-medium px-2 py-0.5 rounded-full">—</span>
                </div>
            </div>
            <button onclick="fecharModalDetalhe()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Corpo scrollável -->
        <div class="flex-1 overflow-y-auto">
            <!-- Indicador de carregamento -->
            <div id="det-loading" class="flex items-center justify-center py-16 text-gray-400">
                <svg class="animate-spin w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                A carregar…
            </div>

            <!-- Conteúdo real (oculto até carregar) -->
            <div id="det-conteudo" class="hidden grid grid-cols-1 lg:grid-cols-3 gap-0 divide-y lg:divide-y-0 lg:divide-x divide-gray-100 dark:divide-gray-800">

                <!-- Coluna esquerda: dados + documentos -->
                <div class="p-6">
                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Dados do Cliente</h4>
                    <dl id="det-dados" class="space-y-2 text-sm mb-5"></dl>

                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3 pt-4 border-t border-gray-100 dark:border-gray-800">Documentos / Propostas</h4>
                    <div id="det-documentos" class="space-y-2"></div>
                </div>

                <!-- Coluna direita: histórico + nova nota -->
                <div class="p-6 lg:col-span-2 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Histórico de Contacto</h4>
                    </div>
                    <div id="det-historico" class="flex-1 space-y-3 overflow-y-auto max-h-72 pr-1"></div>

                    <!-- Formulário nova nota (AJAX) -->
                    <div class="border-t border-gray-100 dark:border-gray-800 pt-4">
                        <div class="flex gap-2">
                            <textarea id="nota-texto" rows="2"
                                      class="flex-1 px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-brand-500 outline-none resize-none"
                                      placeholder="Adicionar nota ao histórico…"></textarea>
                            <button onclick="guardarNota()"
                                    class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm rounded-lg transition self-end">
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const CSRF_TOKEN   = <?= json_encode(csrfToken()) ?>;
const APP_URL      = <?= json_encode(APP_URL) ?>;
let   detalheLeadId = null;

// ── Modal: Criar / Editar Lead ────────────────────────────────
function abrirModalLead(lead = null) {
    const m = document.getElementById('modal-lead');
    document.getElementById('modal-lead-titulo').textContent = lead ? 'Editar Lead' : 'Nova Lead';
    document.getElementById('form-lead-id').value  = lead?.id       ?? 0;
    document.getElementById('form-nome').value     = lead?.nome_cliente ?? '';
    document.getElementById('form-email').value    = lead?.email    ?? '';
    document.getElementById('form-telefone').value = lead?.telefone ?? '';
    document.getElementById('form-nif').value      = lead?.nif      ?? '';
    document.getElementById('form-cc').value       = lead?.cc       ?? '';
    document.getElementById('form-morada').value   = lead?.morada   ?? '';
    document.getElementById('form-estado').value   = lead?.estado   ?? 'Nova Lead';
    m.classList.remove('hidden'); m.classList.add('flex');
    lucide.createIcons();
}
function fecharModalLead() {
    const m = document.getElementById('modal-lead');
    m.classList.add('hidden'); m.classList.remove('flex');
}

// ── Modal: Proposta ───────────────────────────────────────────
function abrirModalProposta(leadId, nome) {
    document.getElementById('proposta-lead-id').value = leadId;
    document.getElementById('proposta-cliente').textContent = nome;
    const m = document.getElementById('modal-proposta');
    m.classList.remove('hidden'); m.classList.add('flex');
    lucide.createIcons();
}
function fecharModalProposta() {
    const m = document.getElementById('modal-proposta');
    m.classList.add('hidden'); m.classList.remove('flex');
}

// ── Modal: Detalhe da Lead (carrega via AJAX) ─────────────────
function abrirModalDetalhe(leadId) {
    detalheLeadId = leadId;
    const m = document.getElementById('modal-detalhe');

    // Reset visual
    document.getElementById('det-loading').classList.remove('hidden');
    document.getElementById('det-conteudo').classList.add('hidden');
    document.getElementById('det-nome').textContent   = '—';
    document.getElementById('det-avatar').textContent = '?';
    document.getElementById('nota-texto').value = '';

    m.classList.remove('hidden'); m.classList.add('flex');

    // Carrega dados via fetch
    fetch(APP_URL + '/lead_ajax.php?id=' + leadId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => renderDetalhe(data))
    .catch(() => {
        document.getElementById('det-loading').textContent = 'Erro ao carregar. Tenta novamente.';
    });
}

function renderDetalhe(d) {
    const lead = d.lead;
    const isDark = document.documentElement.classList.contains('dark');

    // Cabeçalho
    document.getElementById('det-nome').textContent   = lead.nome_cliente;
    document.getElementById('det-avatar').textContent = lead.nome_cliente.charAt(0).toUpperCase();

    const badgeMap = {
        'Nova Lead':         'badge-nova',
        'Contacto Efetuado': 'badge-contacto',
        'Proposta Enviada':  'badge-proposta',
        'Ganho':             'badge-ganho',
        'Perdido':           'badge-perdido',
    };
    const badge = document.getElementById('det-badge');
    badge.textContent  = lead.estado;
    badge.className = 'text-xs font-medium px-2 py-0.5 rounded-full ' + (badgeMap[lead.estado] ?? 'bg-gray-100 text-gray-600');

    // Dados
    const campos = {Email:'email', Telefone:'telefone', NIF:'nif', CC:'cc', Morada:'morada'};
    let dadosHtml = '';
    for (const [label, key] of Object.entries(campos)) {
        if (lead[key]) {
            dadosHtml += `<div class="flex gap-2">
                <dt class="text-gray-400 dark:text-gray-500 w-20 flex-shrink-0 text-xs">${label}</dt>
                <dd class="text-gray-900 dark:text-white text-xs">${escHtml(lead[key])}</dd>
            </div>`;
        }
    }
    document.getElementById('det-dados').innerHTML = dadosHtml || '<p class="text-xs text-gray-400">Sem dados adicionais.</p>';

    // Documentos
    let docsHtml = '';
    if (d.documentos.length === 0) {
        docsHtml = '<p class="text-xs text-gray-400 dark:text-gray-500">Sem documentos anexados.</p>';
    } else {
        d.documentos.forEach(doc => {
            docsHtml += `<div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <a href="${APP_URL}/${escHtml(doc.caminho_ficheiro)}" target="_blank"
                   class="text-xs text-brand-600 hover:underline truncate">${escHtml(doc.nome_documento)}</a>
                <span class="text-xs text-gray-400 flex-shrink-0">${escHtml(doc.data_upload)}</span>
            </div>`;
        });
    }
    document.getElementById('det-documentos').innerHTML = docsHtml;

    // Histórico
    renderHistorico(d.historico);

    // Mostra conteúdo
    document.getElementById('det-loading').classList.add('hidden');
    document.getElementById('det-conteudo').classList.remove('hidden');
    document.getElementById('det-conteudo').classList.add('grid');
    lucide.createIcons();
}

function renderHistorico(historico) {
    let html = '';
    if (historico.length === 0) {
        html = '<p class="text-sm text-gray-400 dark:text-gray-500">Sem registos de contacto.</p>';
    } else {
        historico.forEach(h => {
            html += `<div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-medium text-brand-600 dark:text-brand-400">${escHtml(h.autor)}</span>
                    <span class="text-xs text-gray-400">${escHtml(h.data_registo)}</span>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300">${escHtml(h.descricao).replace(/\n/g,'<br>')}</p>
            </div>`;
        });
    }
    document.getElementById('det-historico').innerHTML = html;
}

function guardarNota() {
    const nota = document.getElementById('nota-texto').value.trim();
    if (!nota || !detalheLeadId) return;

    const btn = document.querySelector('[onclick="guardarNota()"]');
    btn.disabled = true; btn.textContent = '…';

    const fd = new FormData();
    fd.append('csrf_token', CSRF_TOKEN);
    fd.append('acao',       'adicionar_nota');
    fd.append('lead_id',    detalheLeadId);
    fd.append('nota',       nota);

    fetch(APP_URL + '/leads.php', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            document.getElementById('nota-texto').value = '';
            // Recarrega o histórico
            fetch(APP_URL + '/lead_ajax.php?id=' + detalheLeadId, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(d => renderHistorico(d.historico));
        }
    })
    .finally(() => { btn.disabled = false; btn.textContent = 'Guardar'; });
}

function fecharModalDetalhe() {
    const m = document.getElementById('modal-detalhe');
    m.classList.add('hidden'); m.classList.remove('flex');
    detalheLeadId = null;
}

function escHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}

// Fecha ao clicar fora
['modal-lead','modal-proposta','modal-detalhe'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
            this.classList.remove('flex');
        }
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
