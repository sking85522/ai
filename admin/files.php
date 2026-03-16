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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'upload' && isset($_FILES['new_file'])) {
        $fname = basename($_FILES['new_file']['name']);
        $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
        $allowed = ['php', 'html', 'css', 'js', 'json', 'txt', 'md', 'sql', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'csv', 'xml'];

        if (!in_array($ext, $allowed)) {
            $msg = "Upload failed. Extension '.$ext' is not allowed."; $msgClass = 'err';
        } elseif (move_uploaded_file($_FILES['new_file']['tmp_name'], $currentPath . $ds . $fname)) {
            $msg = "Uploaded: $fname";
        } else {
            $msg = "Upload failed."; $msgClass = 'err';
        }
    }

    if ($postAction === 'mkdir') {
        $dname = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_POST['dirname'] ?? '');
        if ($dname && !file_exists($currentPath . $ds . $dname)) {
            mkdir($currentPath . $ds . $dname);
            $msg = "Created directory: $dname";
        } else {
            $msg = "Invalid name or directory exists."; $msgClass = 'err';
        }
    }

    if ($postAction === 'save_file') {
        $f = $_POST['file'] ?? '';
        $content = $_POST['content'] ?? '';
        $fpath = realpath($currentPath . $ds . $f);
        if ($fpath && strpos($fpath, $currentPath) === 0 && is_file($fpath)) {
            file_put_contents($fpath, $content);
            $msg = "File saved: $f";
        } else {
            $msg = "Save failed."; $msgClass = 'err';
        }
    }
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

    <div style="margin-bottom: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
        <form method="post" enctype="multipart/form-data" style="display:inline-block; margin-right:1rem;">
            <input type="hidden" name="action" value="upload">
            <input type="file" name="new_file" required>
            <button type="submit">Upload</button>
        </form>
        <form method="post" style="display:inline-block;">
            <input type="hidden" name="action" value="mkdir">
            <input type="text" name="dirname" placeholder="New Folder Name" required pattern="[a-zA-Z0-9_\-]+">
            <button type="submit">Create Folder</button>
        </form>
    </div>

    <?php if (($action === 'view' || $action === 'edit') && $file && is_file($currentPath . $ds . $file)): ?>
        <h3><?php echo $action === 'edit' ? 'Editing' : 'Viewing'; ?>: <?php echo htmlspecialchars($file); ?></h3>
        <?php if ($action === 'edit'): ?>
            <form method="post">
                <input type="hidden" name="action" value="save_file">
                <input type="hidden" name="file" value="<?php echo htmlspecialchars($file); ?>">
                <textarea name="content" style="width:100%; height:60vh; font-family:monospace; padding:10px; border:1px solid #ccc;"><?php echo htmlspecialchars(file_get_contents($currentPath . $ds . $file)); ?></textarea>
                <br><button type="submit" style="margin-top:10px;">Save Changes</button> <a href="?path=<?php echo urlencode($relativePath); ?>" style="margin-left:10px;">Cancel</a>
            </form>
        <?php else: ?>
            <pre style="background:#eee; padding:1rem; border-radius:8px; max-height:60vh; overflow:auto;"><?php echo htmlspecialchars(file_get_contents($currentPath . $ds . $file)); ?></pre>
        <?php endif; ?>
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
                    <?php if ($isText): ?>
                        <a href="?path=<?php echo urlencode($relativePath); ?>&file=<?php echo urlencode($name); ?>&action=view">View</a> | 
                        <a href="?path=<?php echo urlencode($relativePath); ?>&file=<?php echo urlencode($name); ?>&action=edit">Edit</a> | 
                    <?php endif; ?>
                    <a href="?path=<?php echo urlencode($relativePath); ?>&file=<?php echo urlencode($name); ?>&action=delete" onclick="return confirm('Are you sure you want to delete this file?')" style="color:#c62828">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php admin_footer(); ?>