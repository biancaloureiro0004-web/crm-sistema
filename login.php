<?php
/**
 * login.php — Autenticação segura
 * Corrigido: cookie_samesite='Lax' para compatibilidade local (XAMPP)
 */

require_once __DIR__ . '/config.php';

session_start([
    'cookie_httponly' => true,
    'cookie_secure'   => (defined('APP_ENV') && APP_ENV === 'production'),
    'cookie_samesite' => 'Lax',   // 'Strict' bloqueia cookies após redirect no localhost
]);

if (!empty($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

$erro  = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tokenPost   = $_POST['csrf_token'] ?? '';
    $tokenSessao = $_SESSION['csrf_token'] ?? '';

    if (empty($tokenPost) || empty($tokenSessao) || !hash_equals($tokenSessao, $tokenPost)) {
        $erro = 'Sessão inválida ou expirada. Por favor, tenta novamente.';
    } else {
        $email    = strtolower(trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? ''));
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $erro = 'Por favor preenche todos os campos.';
        } else {
            try {
                $pdo  = getDB();
                $stmt = $pdo->prepare(
                    'SELECT id, nome, email, password, role, estado
                       FROM utilizadores
                      WHERE LOWER(email) = :email
                      LIMIT 1'
                );
                $stmt->execute([':email' => $email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    if ($user['estado'] !== 'ativo') {
                        $erro = 'A tua conta está desativada. Contacta o administrador.';
                    } else {
                        // Login bem-sucedido
                        session_regenerate_id(true);

                        $_SESSION['user_id'] = (int) $user['id'];
                        $_SESSION['nome']    = $user['nome'];
                        $_SESSION['email']   = $user['email'];
                        $_SESSION['role']    = $user['role'];

                        // Limpa o token antigo; será gerado novo na próxima página
                        unset($_SESSION['csrf_token']);

                        redirect('dashboard.php');
                    }
                } else {
                    $erro = 'Credenciais inválidas. Verifica o email e a password.';
                }
            } catch (Exception $e) {
                $erro = APP_ENV === 'development'
                    ? 'Erro: ' . $e->getMessage()
                    : 'Erro interno. Tenta novamente.';
            }
        }
    }
}

$csrfToken = csrfToken();
?>
<!DOCTYPE html>
<html lang="pt" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { colors: { brand: { 100:'#dde7ff',500:'#516df8',600:'#3a4eed',700:'#2f3dd8',900:'#272f88' } } } }
        }
    </script>
    <!-- Aplica dark antes do render para evitar flash branco -->
    <script>(function(){ if(localStorage.getItem('tema')==='escuro') document.documentElement.classList.add('dark'); })();</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="h-full bg-gradient-to-br from-brand-900 via-brand-700 to-indigo-900 flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden">

        <!-- Cabeçalho colorido -->
        <div class="bg-gradient-to-r from-brand-600 to-brand-700 px-8 py-8 text-white text-center">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold"><?= APP_NAME ?></h1>
            <p class="text-brand-100 text-sm mt-1">Sistema de Gestão de Clientes</p>
        </div>

        <!-- Formulário -->
        <div class="px-8 py-8">
            <?php if ($erro): ?>
            <div class="mb-5 bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm px-4 py-3 rounded-lg flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <?= h($erro) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="login.php" novalidate>
                <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>">

                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= h($email) ?>"
                           autocomplete="email" required
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                           placeholder="utilizador@empresa.pt">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                               autocomplete="current-password" required
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition pr-10"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2.5 px-4 rounded-lg transition text-sm">
                    Entrar
                </button>
            </form>
        </div>
    </div>

    <p class="text-center text-white/50 text-xs mt-6">
        &copy; <?= date('Y') ?> <?= APP_NAME ?> — Todos os direitos reservados
    </p>
</div>

<script>
function togglePassword() {
    const i = document.getElementById('password');
    i.type = i.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
