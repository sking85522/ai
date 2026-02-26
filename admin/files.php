<?php
require_once __DIR__ . '/_ui.php';

$baseDir = realpath(dirname(__DIR__)); // Project root
$ds = DIRECTORY_SEPARATOR;
$msg = '';
$msgClass = 'ok';

$relativePath = trim(str_replace('..', '', $_GET['path'] ?? ''), '/\\');
$currentPath = realpath($baseDir . $ds . $relativePath);

// Security check: ensure we are inside the project directory
if (!$currentPath || strpos($currentPath, $baseDir) !== 0) {
    $currentPath = $baseDir;
    $relativePath = '';
} else {
    $relativePath = str_replace($baseDir, '', $currentPath);
}

$action = $_GET['action'] ?? '';
$file = $_GET['file'] ?? '';

if ($action === 'delete' && $file) {
    $filePath = realpath($currentPath . $ds . $file);
    if ($filePath && strpos($filePath, $currentPath) === 0 && is_file($filePath)) {
        if (unlink($filePath)) {
            $msg = "File '$file' deleted successfully.";
        } else {
            $msg = "Failed to delete file '$file'."; $msgClass = 'err';
        }
    } else {
        $msg = "Invalid file or permission denied."; $msgClass = 'err';
    }
}

admin_header('File Manager');

$files = [];
$dirs = [];
if (is_dir($currentPath)) {
    foreach (scandir($currentPath) as $item) {
        if ($item === '.') continue;
        if ($item === '..') {
            if ($currentPath !== $baseDir) $dirs['..'] = 'Parent Directory';
            continue;
        }
        if (is_dir($currentPath . $ds . $item)) $dirs[$item] = $item;
        else $files[$item] = $item;
    }
    ksort($dirs);
    ksort($files);
}

$breadcrumbs = explode($ds, trim($relativePath, $ds));
?>
<div class="card">
    <h2>File Manager</h2>
    <p class="muted">Browse and manage project files. Base: <code><?php echo htmlspecialchars($baseDir); ?></code></p>
    <?php if ($msg): ?><div class="<?php echo $msgClass; ?>"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
</div>

<div class="card">
    <p>
        <a href="?path=/">Root</a> /
        <?php
        $path_so_far = '';
        foreach ($breadcrumbs as $i => $crumb) {
            if (empty($crumb)) continue;
            $path_so_far .= $ds . $crumb;
            if ($i < count($breadcrumbs) - 1) {
                echo '<a href="?path=' . urlencode($path_so_far) . '">' . htmlspecialchars($crumb) . '</a> / ';
            } else {
                echo htmlspecialchars($crumb);
            }
        }
        ?>
    </p>

    <?php if ($action === 'view' && $file && is_file($currentPath . $ds . $file)): ?>
        <h3>Viewing: <?php echo htmlspecialchars($file); ?></h3>
        <pre style="background:#eee; padding:1rem; border-radius:8px; max-height:60vh; overflow:auto;"><?php echo htmlspecialchars(file_get_contents($currentPath . $ds . $file)); ?></pre>
    <?php endif; ?>

    <table>
        <thead><tr><th>Name</th><th>Size (bytes)</th><th>Modified</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($dirs as $name => $label): ?>
            <tr>
                <td><a href="?path=<?php echo urlencode($relativePath . $ds . $name); ?>">📁 <?php echo htmlspecialchars($label); ?></a></td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
        <?php endforeach; ?>
        <?php foreach ($files as $name):
            $filePath = $currentPath . $ds . $name;
            $isText = in_array(pathinfo($name, PATHINFO_EXTENSION), ['php', 'js', 'css', 'html', 'json', 'txt', 'md', 'sql', 'log']);
        ?>
            <tr>
                <td>📄 <?php echo htmlspecialchars($name); ?></td>
                <td><?php echo filesize($filePath); ?></td>
                <td><?php echo date('Y-m-d H:i:s', filemtime($filePath)); ?></td>
                <td>
                    <?php if ($isText): ?><a href="?path=<?php echo urlencode($relativePath); ?>&file=<?php echo urlencode($name); ?>&action=view">View</a> | <?php endif; ?>
                    <a href="?path=<?php echo urlencode($relativePath); ?>&file=<?php echo urlencode($name); ?>&action=delete" onclick="return confirm('Are you sure you want to delete this file?')" style="color:#c62828">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php admin_footer(); ?>