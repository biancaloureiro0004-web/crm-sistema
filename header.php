<?php
/**
 * includes/header.php
 * Layout principal — barra lateral + cabeçalho
 * v2: Suporte a Tema Escuro (Dark Mode) com localStorage
 */
$pageTitle = $pageTitle ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="pt" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?> — <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#f0f4ff',
                            100: '#dde7ff',
                            200: '#c3d3ff',
                            300: '#9db4fe',
                            400: '#7490fc',
                            500: '#516df8',
                            600: '#3a4eed',
                            700: '#2f3dd8',
                            800: '#2933ae',
                            900: '#272f88',
                            950: '#1a1e52',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Aplica dark mode ANTES de renderizar para evitar flash branco -->
    <script>
        (function() {
            if (localStorage.getItem('tema') === 'escuro') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* ── Sidebar links ── */
        .sidebar-link { border-left: 3px solid transparent; }
        .sidebar-link.active {
            background-color: rgba(81,109,248,0.15);
            color: #516df8;
            border-left: 3px solid #516df8;
        }

        /* ── Badges de estado de lead ── */
        .badge-nova      { background:#dbeafe; color:#1e40af; }
        .badge-contacto  { background:#fef9c3; color:#854d0e; }
        .badge-proposta  { background:#ede9fe; color:#5b21b6; }
        .badge-ganho     { background:#dcfce7; color:#166534; }
        .badge-perdido   { background:#fee2e2; color:#991b1b; }
        .badge-equipa    { background:#fef3c7; color:#92400e; }

        /* ── Dark mode: badges (cores ajustadas para melhor contraste) ── */
        .dark .badge-nova     { background:#1e3a5f; color:#93c5fd; }
        .dark .badge-contacto { background:#451a03; color:#fcd34d; }
        .dark .badge-proposta { background:#2e1065; color:#c4b5fd; }
        .dark .badge-ganho    { background:#052e16; color:#86efac; }
        .dark .badge-perdido  { background:#450a0a; color:#fca5a5; }
        .dark .badge-equipa   { background:#451a03; color:#fde68a; }

        /* ── Dark mode: scrollbar ── */
        .dark ::-webkit-scrollbar { width: 6px; height: 6px; }
        .dark ::-webkit-scrollbar-track { background: #1e2030; }
        .dark ::-webkit-scrollbar-thumb { background: #3a4eed; border-radius: 3px; }

        /* ── Transição suave de tema ── */
        *, *::before, *::after { transition: background-color 0.2s ease, border-color 0.2s ease, color 0.15s ease; }
    </style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100">

<div class="flex h-screen overflow-hidden">

    <!-- ════════════════════════════════════════════════════
         BARRA LATERAL
    ════════════════════════════════════════════════════ -->
    <aside class="w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col shadow-sm flex-shrink-0">

        <!-- Logótipo + botão de tema -->
        <div class="flex items-center gap-2 px-5 py-5 border-b border-gray-100 dark:border-gray-800">
            <div class="w-8 h-8 rounded-lg bg-brand-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="font-bold text-gray-900 dark:text-white text-base tracking-tight flex-1 truncate"><?= APP_NAME ?></span>
            <!-- Botão alternância de tema Sol/Lua -->
            <button id="btn-tema"
                    onclick="alternarTema()"
                    title="Alternar tema claro/escuro"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition flex-shrink-0">
                <!-- Ícone Sol (visível no dark mode — clica para voltar a claro) -->
                <svg id="icon-sol" class="w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 3v1m0 16v1m8.66-9H21M3 12H2m15.07-6.07l-.71.71M6.64 17.36l-.71.71m12.02 0l-.71-.71M6.64 6.64l-.71-.71M12 7a5 5 0 100 10A5 5 0 0012 7z"/>
                </svg>
                <!-- Ícone Lua (visível no light mode — clica para ir a escuro) -->
                <svg id="icon-lua" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/>
                </svg>
            </button>
        </div>

        <!-- Navegação -->
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            <?php
            $currentPage = basename($_SERVER['PHP_SELF']);
            $menuItems = [
                ['href' => 'dashboard.php',  'label' => 'Dashboard',   'icon' => 'layout-dashboard', 'pages' => ['dashboard.php']],
                ['href' => 'leads.php',      'label' => 'Leads',       'icon' => 'users',             'pages' => ['leads.php']],
                ['href' => 'calendario.php', 'label' => 'Calendário',  'icon' => 'calendar',          'pages' => ['calendario.php']],
            ];
            if (hasRole('admin')) {
                $menuItems[] = ['href' => 'funcionarios.php', 'label' => 'Equipa',     'icon' => 'user-cog',    'pages' => ['funcionarios.php']];
                $menuItems[] = ['href' => 'rh.php',           'label' => 'RH / Docs',  'icon' => 'folder-open', 'pages' => ['rh.php']];
                $menuItems[] = ['href' => 'relatorios.php',   'label' => 'Relatórios', 'icon' => 'bar-chart-2', 'pages' => ['relatorios.php']];
            }
            foreach ($menuItems as $item):
                $active = in_array($currentPage, $item['pages']) ? 'active' : '';
            ?>
            <a href="<?= APP_URL ?>/<?= $item['href'] ?>"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                      text-gray-600 dark:text-gray-400
                      hover:bg-gray-50 dark:hover:bg-gray-800
                      hover:text-gray-900 dark:hover:text-white
                      transition-colors <?= $active ?>">
                <i data-lucide="<?= $item['icon'] ?>" class="w-4 h-4 flex-shrink-0"></i>
                <?= $item['label'] ?>
            </a>
            <?php endforeach; ?>
        </nav>

        <!-- Utilizador logado -->
        <div class="px-4 py-4 border-t border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-900 flex items-center justify-center text-brand-700 dark:text-brand-300 font-semibold text-sm flex-shrink-0">
                    <?= strtoupper(substr($_SESSION['nome'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?= h($_SESSION['nome'] ?? '') ?></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 capitalize"><?= h($_SESSION['role'] ?? '') ?></p>
                </div>
                <a href="<?= APP_URL ?>/logout.php" title="Terminar sessão"
                   class="text-gray-400 hover:text-red-500 transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- ════════════════════════════════════════════════════
         CONTEÚDO PRINCIPAL
    ════════════════════════════════════════════════════ -->
    <main class="flex-1 overflow-y-auto">

        <!-- Barra superior da página -->
        <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white"><?= h($pageTitle) ?></h1>
            <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500">
                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                <?= date('d/m/Y H:i') ?>
            </div>
        </header>

        <!-- Flash: sucesso -->
        <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="mx-8 mt-4 bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 text-sm px-4 py-3 rounded-lg flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0 text-green-600 dark:text-green-400"></i>
            <?= h($_SESSION['flash_success']) ?>
        </div>
        <?php unset($_SESSION['flash_success']); endif; ?>

        <!-- Flash: erro -->
        <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="mx-8 mt-4 bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 text-sm px-4 py-3 rounded-lg flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0 text-red-600 dark:text-red-400"></i>
            <?= h($_SESSION['flash_error']) ?>
        </div>
        <?php unset($_SESSION['flash_error']); endif; ?>

        <div class="px-8 py-6">
<!-- ↑ Fechado em footer.php -->

<script>
// ── Dark Mode: lógica de alternância ────────────────────────
function alternarTema() {
    const html    = document.documentElement;
    const isDark  = html.classList.toggle('dark');
    localStorage.setItem('tema', isDark ? 'escuro' : 'claro');
    actualizarIconesTema(isDark);
}

function actualizarIconesTema(isDark) {
    document.getElementById('icon-sol').classList.toggle('hidden', !isDark);
    document.getElementById('icon-lua').classList.toggle('hidden', isDark);
}

// Aplica o estado correcto dos ícones assim que o DOM estiver pronto
(function() {
    const isDark = document.documentElement.classList.contains('dark');
    actualizarIconesTema(isDark);
})();
</script>
