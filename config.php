<?php
/**
 * config.php — Configuração central da aplicação
 * CRM - Serviços e Reparações Técnicas
 *
 * IMPORTANTE: Este ficheiro NÃO deve ser exposto publicamente.
 * Coloca-o fora da pasta public_html em produção, ou protege com .htaccess.
 */

// ─── Ambiente ───────────────────────────────────────────────
define('APP_ENV',  'development');  // 'development' | 'production'
define('APP_NAME', 'TécnicoCRM');
define('APP_URL',  'http://localhost/crm'); // URL base da aplicação

// ─── Base de Dados ───────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'crm_servicos');
define('DB_USER',    'root');
define('DB_PASS',    '');           // Alterar em produção!
define('DB_CHARSET', 'utf8mb4');

// ─── Email (PHPMailer / SMTP) ────────────────────────────────
define('MAIL_HOST',       'smtp.gmail.com');
define('MAIL_PORT',       587);
define('MAIL_USERNAME',   'seuemail@gmail.com');    // Alterar!
define('MAIL_PASSWORD',   'sua_app_password');      // Alterar!
define('MAIL_FROM',       'seuemail@gmail.com');
define('MAIL_FROM_NAME',  APP_NAME);
define('MAIL_ENCRYPTION', 'tls');    // 'tls' ou 'ssl'

// ─── Uploads ────────────────────────────────────────────────
define('UPLOAD_BASE_PATH', __DIR__ . '/uploads/');
define('UPLOAD_MAX_SIZE',  10 * 1024 * 1024); // 10 MB
define('UPLOAD_ALLOWED_TYPES', ['application/pdf', 'image/jpeg', 'image/png']);

// ─── Sessão ──────────────────────────────────────────────────
define('SESSION_LIFETIME', 3600); // segundos (1 hora)

// ─── Erros (desativar em produção) ──────────────────────────
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// ─── Conexão PDO (singleton) ────────────────────────────────
function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST, DB_NAME, DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Em produção, regista o erro em log e mostra mensagem genérica
            if (APP_ENV === 'development') {
                die('Erro de conexão à base de dados: ' . $e->getMessage());
            }
            die('Erro interno. Por favor tente mais tarde.');
        }
    }

    return $pdo;
}

// ─── Funções utilitárias ────────────────────────────────────

/**
 * Sanitiza output para prevenir XSS
 */
function h(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Redireciona para uma URL relativa à aplicação
 */
function redirect(string $path): never
{
    header('Location: ' . APP_URL . '/' . ltrim($path, '/'));
    exit;
}

/**
 * Verifica se o utilizador tem uma determinada role
 */
function hasRole(string $role): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Força o utilizador a ser admin; caso contrário redireciona
 */
function requireAdmin(): void
{
    if (!hasRole('admin')) {
        redirect('dashboard.php?erro=acesso_negado');
    }
}

/**
 * Força o utilizador a estar autenticado
 */
function requireAuth(): void
{
    if (empty($_SESSION['user_id'])) {
        redirect('login.php');
    }
}

/**
 * Gera um token CSRF e guarda na sessão
 */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida o token CSRF de um formulário POST
 */
function validateCsrf(): void
{
    if (
        empty($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
    ) {
        http_response_code(403);
        die('Token CSRF inválido. Por favor recarrega a página e tenta novamente.');
    }
    // Regenera token após validação
    //unset($_SESSION['csrf_token']);
}

/**
 * Faz upload seguro de um ficheiro PDF
 * Devolve o caminho relativo ou lança uma exceção em caso de erro
 */
function uploadFicheiro(array $file, string $subdir): string
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Erro no upload do ficheiro.');
    }

    if ($file['size'] > UPLOAD_MAX_SIZE) {
        throw new RuntimeException('O ficheiro excede o tamanho máximo de 10 MB.');
    }

    // Valida o tipo MIME real (não apenas a extensão)
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES, true)) {
        throw new RuntimeException('Tipo de ficheiro não permitido. Apenas PDF, JPG e PNG.');
    }

    $ext       = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename  = uniqid('', true) . '_' . time() . '.' . strtolower($ext);
    $targetDir = UPLOAD_BASE_PATH . rtrim($subdir, '/') . '/';

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $targetPath = $targetDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Não foi possível mover o ficheiro para o destino.');
    }

    return 'uploads/' . rtrim($subdir, '/') . '/' . $filename;
}
