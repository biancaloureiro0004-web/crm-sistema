<?php
/**
 * lead_ajax.php — Endpoint AJAX para os detalhes de uma Lead
 * Chamado pelo modal de detalhe em leads.php
 * Retorna JSON com: lead, historico, documentos
 */

require_once __DIR__ . '/config.php';

session_start([
    'cookie_httponly' => true,
    'cookie_secure'   => (APP_ENV === 'production'),
    'cookie_samesite' => 'Lax',
]);

header('Content-Type: application/json; charset=utf-8');

// Apenas aceita pedidos AJAX autenticados
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || empty($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['erro' => 'Acesso negado.']);
    exit;
}

$leadId  = (int) ($_GET['id'] ?? 0);
$userId  = (int) $_SESSION['user_id'];
$isAdmin = hasRole('admin');

if ($leadId <= 0) {
    echo json_encode(['erro' => 'ID inválido.']);
    exit;
}

$pdo = getDB();

// ── Dados da Lead ─────────────────────────────────────────────
$stmt = $pdo->prepare('SELECT * FROM leads WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $leadId]);
$lead = $stmt->fetch();

if (!$lead) {
    echo json_encode(['erro' => 'Lead não encontrada.']);
    exit;
}

// Verificação de permissão: funcionário só vê as suas
if (!$isAdmin && (int)$lead['criado_por'] !== $userId) {
    http_response_code(403);
    echo json_encode(['erro' => 'Sem permissão.']);
    exit;
}

// ── Histórico ─────────────────────────────────────────────────
$stmtH = $pdo->prepare(
    'SELECT h.descricao, DATE_FORMAT(h.data_registo, "%d/%m/%Y %H:%i") AS data_registo, u.nome AS autor
       FROM historico_contacto h
       JOIN utilizadores u ON u.id = h.utilizador_id
      WHERE h.lead_id = :lid
      ORDER BY h.data_registo DESC'
);
$stmtH->execute([':lid' => $leadId]);
$historico = $stmtH->fetchAll();

// ── Documentos ────────────────────────────────────────────────
$stmtD = $pdo->prepare(
    'SELECT d.nome_documento, d.caminho_ficheiro,
            DATE_FORMAT(d.data_upload, "%d/%m/%Y") AS data_upload,
            u.nome AS enviado_por_nome
       FROM documentos_leads d
       JOIN utilizadores u ON u.id = d.enviado_por
      WHERE d.lead_id = :lid
      ORDER BY d.data_upload DESC'
);
$stmtD->execute([':lid' => $leadId]);
$documentos = $stmtD->fetchAll();

// ── Resposta JSON ─────────────────────────────────────────────
echo json_encode([
    'lead'       => $lead,
    'historico'  => $historico,
    'documentos' => $documentos,
], JSON_UNESCAPED_UNICODE);
