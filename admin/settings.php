<?php
require_once __DIR__ . '/_ui.php';

$msg = '';
$msgClass = 'ok';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'reset_file_memory') {
        $path = dirname(__DIR__) . '/storage/training/memory_store.json';
        file_put_contents($path, json_encode(['facts' => []], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $msg = 'File memory reset complete.';
        $msgClass = 'ok';
    }

    if ($action === 'truncate_conversations') {
        $pdo = Db::pdo();
        if ($pdo) {
            $pdo->exec('TRUNCATE TABLE conversations');
            $msg = 'Conversations table truncated.';
            $msgClass = 'ok';
        } else {
            $msg = 'Database not connected.';
            $msgClass = 'err';
        }
    }
}

admin_header('Settings');
?>
<div class="card">
    <h2>System Settings</h2>
    <p class="muted">Maintenance actions for memory and logs.</p>
    <?php if ($msg): ?><div class="<?php echo $msgClass; ?>"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
</div>

<div class="card">
    <h3>Storage Paths</h3>
    <table>
        <tr><th>Path</th><th>Purpose</th></tr>
        <tr><td><?php echo htmlspecialchars(realpath(dirname(__DIR__) . '/storage/training')); ?></td><td>Training files</td></tr>
        <tr><td><?php echo htmlspecialchars(realpath(dirname(__DIR__) . '/storage/logs')); ?></td><td>Log storage</td></tr>
        <tr><td><?php echo htmlspecialchars(realpath(dirname(__DIR__) . '/storage/cache')); ?></td><td>Cache storage</td></tr>
    </table>
</div>

<div class="card">
    <h3>Maintenance</h3>
    <form method="post" style="margin-bottom:.6rem">
        <input type="hidden" name="action" value="reset_file_memory">
        <button type="submit">Reset File Memory</button>
    </form>
    <form method="post">
        <input type="hidden" name="action" value="truncate_conversations">
        <button type="submit" style="background:#c62828">Truncate Conversations</button>
    </form>
</div>
<?php admin_footer(); ?>
