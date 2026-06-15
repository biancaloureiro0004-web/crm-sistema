<?php
/**
 * funcionarios.php — Gestão de Equipa (Admin)
 * v2: Dark mode completo + cookie_samesite Lax
 */

require_once __DIR__ . '/config.php';

session_start([
    'cookie_httponly' => true,
    'cookie_secure'   => (APP_ENV === 'production'),
    'cookie_samesite' => 'Lax',
]);

requireAuth();
requireAdmin();

$pdo       = getDB();
$pageTitle = 'Gestão de Equipa';

// ─── POST ─────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF robusto
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403); die('Token CSRF inválido.');
    }

    $acao = $_POST['acao'] ?? '';

    if (in_array($acao, ['criar_funcionario','editar_funcionario'], true)) {
        $funcId  = (int)($_POST['func_id'] ?? 0);
        $nome    = trim($_POST['nome']    ?? '');
        $email   = trim($_POST['email']   ?? '');
        $tel     = trim($_POST['telefone']?? '');
        $cc      = trim($_POST['cc']      ?? '');
        $nif     = trim($_POST['nif']     ?? '');
        $morada  = trim($_POST['morada']  ?? '');
        $cargo   = trim($_POST['cargo']   ?? '');
        $salario = (float)($_POST['salario_base'] ?? 0);
        $estado  = ($_POST['estado'] ?? '') === 'ativo' ? 'ativo' : 'inativo';
        $role    = ($_POST['role']   ?? '') === 'admin' ? 'admin' : 'funcionario';
        $pwd     = trim($_POST['password'] ?? '');

        if (empty($nome) || empty($email)) {
            $_SESSION['flash_error'] = 'Nome e email são obrigatórios.';
            redirect('funcionarios.php');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Email inválido.';
            redirect('funcionarios.php');
        }

        if ($acao === 'criar_funcionario') {
            if (strlen($pwd) < 8) {
                $_SESSION['flash_error'] = 'Password com mínimo 8 caracteres.';
                redirect('funcionarios.php');
            }
            $chk = $pdo->prepare('SELECT id FROM utilizadores WHERE email=:e LIMIT 1');
            $chk->execute([':e' => $email]);
            if ($chk->fetch()) {
                $_SESSION['flash_error'] = 'Email já registado.';
                redirect('funcionarios.php');
            }
            $pdo->prepare(
                'INSERT INTO utilizadores (nome,email,password,telefone,cc,nif,morada,cargo,salario_base,estado,role)
                 VALUES (:n,:e,:p,:t,:cc,:nif,:m,:c,:s,:est,:r)'
            )->execute([':n'=>$nome,':e'=>$email,':p'=>password_hash($pwd,PASSWORD_BCRYPT,['cost'=>12]),
                ':t'=>$tel,':cc'=>$cc,':nif'=>$nif,':m'=>$morada,':c'=>$cargo,
                ':s'=>$salario,':est'=>$estado,':r'=>$role]);
            $_SESSION['flash_success'] = 'Funcionário criado.';
        } else {
            $params = [':n'=>$nome,':e'=>$email,':t'=>$tel,':cc'=>$cc,':nif'=>$nif,
                       ':m'=>$morada,':c'=>$cargo,':s'=>$salario,':est'=>$estado,':r'=>$role,':id'=>$funcId];
            if (!empty($pwd)) {
                if (strlen($pwd) < 8) { $_SESSION['flash_error']='Password min 8 chars.'; redirect('funcionarios.php'); }
                $pdo->prepare(
                    'UPDATE utilizadores SET nome=:n,email=:e,password=:p,telefone=:t,cc=:cc,
                     nif=:nif,morada=:m,cargo=:c,salario_base=:s,estado=:est,role=:r WHERE id=:id'
                )->execute(array_merge($params,[':p'=>password_hash($pwd,PASSWORD_BCRYPT,['cost'=>12])]));
            } else {
                $pdo->prepare(
                    'UPDATE utilizadores SET nome=:n,email=:e,telefone=:t,cc=:cc,
                     nif=:nif,morada=:m,cargo=:c,salario_base=:s,estado=:est,role=:r WHERE id=:id'
                )->execute($params);
            }
            $_SESSION['flash_success'] = 'Funcionário atualizado.';
        }
        redirect('funcionarios.php');
    }

    if ($acao === 'toggle_estado') {
        $funcId = (int)($_POST['func_id'] ?? 0);
        $pdo->prepare("UPDATE utilizadores SET estado=IF(estado='ativo','inativo','ativo') WHERE id=:id")
            ->execute([':id'=>$funcId]);
        $_SESSION['flash_success'] = 'Estado atualizado.';
        redirect('funcionarios.php');
    }
}

// ─── Leitura ──────────────────────────────────────────────────
$filtroEstado = $_GET['estado'] ?? '';
$where  = $filtroEstado ? 'WHERE estado=:est' : '';
$params = $filtroEstado ? [':est'=>$filtroEstado] : [];
$stmt = $pdo->prepare(
    "SELECT u.*, (SELECT COUNT(*) FROM leads l WHERE l.criado_por=u.id) AS total_leads
       FROM utilizadores u {$where} ORDER BY u.estado DESC, u.nome ASC"
);
$stmt->execute($params);
$funcionarios = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<!-- Filtros + botão novo -->
<div class="flex flex-col sm:flex-row gap-4 mb-6">
    <div class="flex gap-2 flex-wrap">
        <?php foreach ([''=> 'Todos','ativo'=>'Ativos','inativo'=>'Inativos'] as $val=>$label): ?>
        <a href="funcionarios.php<?= $val ? '?estado='.$val : '' ?>"
           class="px-4 py-2 text-sm rounded-lg transition
                  <?= $filtroEstado===$val
                      ? 'bg-brand-600 text-white'
                      : 'bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' ?>">
            <?= $label ?>
        </a>
        <?php endforeach; ?>
    </div>
    <button onclick="abrirModalFunc()"
            class="ml-auto flex items-center gap-2 px-4 py-2
                   bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition">
        <i data-lucide="user-plus" class="w-4 h-4"></i> Novo Funcionário
    </button>
</div>

<!-- Grelha de Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
<?php foreach ($funcionarios as $func): ?>

<div class="bg-white dark:bg-gray-900
            rounded-xl border border-gray-200 dark:border-gray-800
            p-5 hover:shadow-sm transition
            <?= $func['estado']==='inativo' ? 'opacity-60' : '' ?>">

    <div class="flex items-start justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-full flex-shrink-0 flex items-center justify-center
                        bg-brand-100 dark:bg-brand-900
                        text-brand-700 dark:text-brand-300 font-bold text-lg">
                <?= strtoupper(substr($func['nome'],0,1)) ?>
            </div>
            <div>
                <p class="font-semibold text-gray-900 dark:text-white"><?= h($func['nome']) ?></p>
                <p class="text-xs text-gray-500 dark:text-gray-400"><?= h($func['cargo'] ?: 'Sem cargo') ?></p>
            </div>
        </div>
        <span class="text-xs font-medium px-2 py-0.5 rounded-full
                     <?= $func['estado']==='ativo'
                         ? 'bg-green-50 dark:bg-green-950 text-green-700 dark:text-green-300'
                         : 'bg-red-50 dark:bg-red-950 text-red-600 dark:text-red-400' ?>">
            <?= ucfirst($func['estado']) ?>
        </span>
    </div>

    <dl class="space-y-1.5 text-sm mb-4">
        <?php foreach(['Email'=>'email','Telemóvel'=>'telefone'] as $l=>$k): ?>
        <div class="flex gap-2">
            <dt class="text-gray-400 dark:text-gray-500 w-20 flex-shrink-0 text-xs"><?= $l ?></dt>
            <dd class="text-gray-700 dark:text-gray-200 truncate text-xs"><?= h($func[$k]?:'—') ?></dd>
        </div>
        <?php endforeach; ?>
        <div class="flex gap-2">
            <dt class="text-gray-400 dark:text-gray-500 w-20 flex-shrink-0 text-xs">Role</dt>
            <dd>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full
                             <?= $func['role']==='admin'
                                 ? 'bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300'
                                 : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300' ?>">
                    <?= ucfirst($func['role']) ?>
                </span>
            </dd>
        </div>
        <div class="flex gap-2">
            <dt class="text-gray-400 dark:text-gray-500 w-20 flex-shrink-0 text-xs">Salário</dt>
            <dd class="text-gray-700 dark:text-gray-200 font-medium text-xs">
                <?= number_format($func['salario_base'],2,',','.') ?> €
            </dd>
        </div>
        <div class="flex gap-2">
            <dt class="text-gray-400 dark:text-gray-500 w-20 flex-shrink-0 text-xs">Leads</dt>
            <dd class="text-gray-700 dark:text-gray-200 text-xs"><?= $func['total_leads'] ?></dd>
        </div>
    </dl>

    <div class="flex gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
        <button onclick="abrirModalFunc(<?= htmlspecialchars(json_encode($func),ENT_QUOTES) ?>)"
                class="flex-1 text-xs font-medium py-1.5 rounded-lg transition flex items-center justify-center gap-1
                       border border-gray-300 dark:border-gray-700
                       text-gray-600 dark:text-gray-300
                       hover:bg-gray-50 dark:hover:bg-gray-800">
            <i data-lucide="edit-2" class="w-3.5 h-3.5"></i> Editar
        </button>

        <form method="POST" action="funcionarios.php" class="flex-1"
              onsubmit="return confirm('Confirma alteração de estado?')">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
            <input type="hidden" name="acao"    value="toggle_estado">
            <input type="hidden" name="func_id" value="<?= $func['id'] ?>">
            <button type="submit"
                    class="w-full text-xs font-medium py-1.5 rounded-lg transition flex items-center justify-center gap-1
                           <?= $func['estado']==='ativo'
                               ? 'border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950'
                               : 'border border-green-200 dark:border-green-800 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-950' ?>">
                <i data-lucide="<?= $func['estado']==='ativo' ? 'user-x' : 'user-check' ?>" class="w-3.5 h-3.5"></i>
                <?= $func['estado']==='ativo' ? 'Desativar' : 'Ativar' ?>
            </button>
        </form>

        <a href="rh.php?func_id=<?= $func['id'] ?>"
           class="px-3 py-1.5 text-xs font-medium rounded-lg transition flex items-center
                  border border-gray-300 dark:border-gray-700
                  text-gray-600 dark:text-gray-300
                  hover:bg-gray-50 dark:hover:bg-gray-800">
            <i data-lucide="folder" class="w-3.5 h-3.5"></i>
        </a>
    </div>
</div>

<?php endforeach; ?>

<?php if (empty($funcionarios)): ?>
<div class="col-span-3 bg-white dark:bg-gray-900
            rounded-xl border border-gray-200 dark:border-gray-800
            py-16 text-center text-gray-400 dark:text-gray-500">
    Nenhum funcionário encontrado.
</div>
<?php endif; ?>
</div>

<!-- Modal: Criar / Editar Funcionário -->
<div id="modal-func"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto">

        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <h3 id="modal-func-titulo" class="font-semibold text-gray-900 dark:text-white text-lg">
                Novo Funcionário
            </h3>
            <button onclick="fecharModalFunc()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="funcionarios.php" class="px-6 py-5">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
            <input type="hidden" name="acao"    id="func-acao"   value="criar_funcionario">
            <input type="hidden" name="func_id" id="func-id"     value="0">

            <?php
            $inputClass = 'w-full px-3 py-2 text-sm rounded-lg outline-none
                           border border-gray-300 dark:border-gray-700
                           bg-white dark:bg-gray-800
                           text-gray-900 dark:text-white
                           focus:ring-2 focus:ring-brand-500 focus:border-brand-500
                           placeholder-gray-400 dark:placeholder-gray-500';
            $labelClass = 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1';
            ?>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="<?= $labelClass ?>">Nome Completo *</label>
                    <input type="text" name="nome" id="func-nome" required class="<?= $inputClass ?>">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Email *</label>
                    <input type="email" name="email" id="func-email" required class="<?= $inputClass ?>">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Telefone</label>
                    <input type="text" name="telefone" id="func-telefone" class="<?= $inputClass ?>">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">NIF</label>
                    <input type="text" name="nif" id="func-nif" class="<?= $inputClass ?>">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">CC</label>
                    <input type="text" name="cc" id="func-cc" class="<?= $inputClass ?>">
                </div>
                <div class="col-span-2">
                    <label class="<?= $labelClass ?>">Morada</label>
                    <input type="text" name="morada" id="func-morada" class="<?= $inputClass ?>">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Cargo</label>
                    <input type="text" name="cargo" id="func-cargo" placeholder="Ex: Técnico Sénior" class="<?= $inputClass ?>">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Salário Base (€)</label>
                    <input type="number" name="salario_base" id="func-salario" step="0.01" min="0" class="<?= $inputClass ?>">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Role</label>
                    <select name="role" id="func-role"
                            class="<?= $inputClass ?> bg-white dark:bg-gray-800">
                        <option value="funcionario">Funcionário</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Estado</label>
                    <select name="estado" id="func-estado"
                            class="<?= $inputClass ?> bg-white dark:bg-gray-800">
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="<?= $labelClass ?>">
                        Password
                        <span id="pass-hint" class="text-gray-400 dark:text-gray-500 font-normal">
                            (mínimo 8 caracteres)
                        </span>
                    </label>
                    <input type="password" name="password" id="func-password"
                           autocomplete="new-password"
                           placeholder="Deixar em branco para manter"
                           class="<?= $inputClass ?>">
                </div>
            </div>

            <div class="flex gap-3 mt-5 pt-4 border-t border-gray-100 dark:border-gray-800">
                <button type="submit"
                        class="flex-1 bg-brand-600 hover:bg-brand-700 text-white
                               text-sm font-medium py-2.5 rounded-lg transition">
                    Guardar
                </button>
                <button type="button" onclick="fecharModalFunc()"
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
function abrirModalFunc(func = null) {
    const m = document.getElementById('modal-func');
    document.getElementById('modal-func-titulo').textContent = func ? 'Editar Funcionário' : 'Novo Funcionário';
    document.getElementById('func-acao').value     = func ? 'editar_funcionario' : 'criar_funcionario';
    document.getElementById('func-id').value       = func?.id       ?? 0;
    document.getElementById('func-nome').value     = func?.nome     ?? '';
    document.getElementById('func-email').value    = func?.email    ?? '';
    document.getElementById('func-telefone').value = func?.telefone ?? '';
    document.getElementById('func-nif').value      = func?.nif      ?? '';
    document.getElementById('func-cc').value       = func?.cc       ?? '';
    document.getElementById('func-morada').value   = func?.morada   ?? '';
    document.getElementById('func-cargo').value    = func?.cargo    ?? '';
    document.getElementById('func-salario').value  = func?.salario_base ?? '0';
    document.getElementById('func-role').value     = func?.role    ?? 'funcionario';
    document.getElementById('func-estado').value   = func?.estado  ?? 'ativo';
    document.getElementById('func-password').value = '';
    document.getElementById('pass-hint').textContent = func
        ? '(deixar em branco para manter)' : '(mínimo 8 caracteres)';
    m.classList.remove('hidden'); m.classList.add('flex');
    lucide.createIcons();
}
function fecharModalFunc() {
    const m = document.getElementById('modal-func');
    m.classList.add('hidden'); m.classList.remove('flex');
}
document.getElementById('modal-func').addEventListener('click', function(e) {
    if (e.target === this) fecharModalFunc();
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
