<?php
require_once __DIR__ . '/_ui.php';

$pdo = Db::pdo();
if (!$pdo) {
    admin_header('Database Error');
    echo '<div class="card err">Database connection failed.</div>';
    admin_footer();
    exit;
}

$table = trim($_GET['table'] ?? '');
$pkName = trim($_GET['pk_name'] ?? '');
$pkValue = trim($_GET['pk_value'] ?? '');
$msg = '';
$msgClass = 'ok';

// Basic validation
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
if ($table === '' || $pkName === '' || $pkValue === '' || !in_array($table, $tables)) {
    admin_header('Error');
    echo '<div class="card err">Invalid request. Table or primary key information is missing.</div>';
    admin_footer();
    exit;
}

// Fetch schema to validate pkName and other columns
$schemaResult = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
$colTypes = [];
foreach ($schemaResult as $col) {
    $colTypes[$col['Field']] = strtolower($col['Type']);
}
$schema = array_keys($colTypes);
if (!in_array($pkName, $schema)) {
    admin_header('Error');
    echo '<div class="card err">Invalid primary key column.</div>';
    admin_footer();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updateValues = $_POST['fields'] ?? [];
    $sqlParts = [];
    $params = [];
    foreach ($updateValues as $col => $val) {
        if (in_array($col, $schema) && $col !== $pkName) {
            $sqlParts[] = "`$col` = ?";
            $params[] = $val;
        }
    }

    if (!empty($sqlParts)) {
        $params[] = $pkValue;
        $sql = "UPDATE `$table` SET " . implode(', ', $sqlParts) . " WHERE `$pkName` = ?";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params) ? ($msg = 'Row updated successfully.') : ($msg = 'Failed to update row.');
        } catch (PDOException $e) {
            $msg = 'Error updating row: ' . $e->getMessage();
            $msgClass = 'err';
        }
    }
}

// Fetch the row data (re-fetch after potential update)
$stmt = $pdo->prepare("SELECT * FROM `$table` WHERE `$pkName` = ?");
$stmt->execute([$pkValue]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    admin_header('Error');
    echo '<div class="card err">Row not found.</div>';
    admin_footer();
    exit;
}

admin_header("Edit Row in $table");
?>
<div class="card">
    <h2>Edit Row in <code><?php echo htmlspecialchars($table); ?></code></h2>
    <p class="muted">Primary Key: <code><?php echo htmlspecialchars($pkName); ?> = <?php echo htmlspecialchars($pkValue); ?></code></p>
    <?php if ($msg): ?><div class="card <?php echo $msgClass; ?>"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
</div>

<div class="card">
    <form method="post">
        <?php foreach ($row as $column => $value): ?>
            <div style="margin-bottom: 1rem;"><label for="field_<?php echo htmlspecialchars($column); ?>"><strong><?php echo htmlspecialchars($column); ?></strong></label>
            <?php 
            if ($column === $pkName) {
                echo '<input type="text" value="' . htmlspecialchars($value) . '" readonly style="background:#eee;">';
            } else {
                $type = $colTypes[$column] ?? 'text';
                if (str_contains($type, 'text') || str_contains($type, 'blob') || str_contains($type, 'json')) {
                    echo '<textarea id="field_' . htmlspecialchars($column) . '" name="fields[' . htmlspecialchars($column) . ']" rows="' . max(3, min(15, substr_count((string)$value, "\n") + 2)) . '">' . htmlspecialchars($value) . '</textarea>';
                } elseif (str_contains($type, 'int') || str_contains($type, 'decimal') || str_contains($type, 'float') || str_contains($type, 'double')) {
                    echo '<input type="number" step="any" id="field_' . htmlspecialchars($column) . '" name="fields[' . htmlspecialchars($column) . ']" value="' . htmlspecialchars($value) . '">';
                } elseif (str_contains($type, 'datetime') || str_contains($type, 'timestamp')) {
                    $val = $value ? date('Y-m-d\TH:i', strtotime($value)) : '';
                    echo '<input type="datetime-local" id="field_' . htmlspecialchars($column) . '" name="fields[' . htmlspecialchars($column) . ']" value="' . htmlspecialchars($val) . '">';
                } elseif (str_contains($type, 'date')) {
                    echo '<input type="date" id="field_' . htmlspecialchars($column) . '" name="fields[' . htmlspecialchars($column) . ']" value="' . htmlspecialchars($value) . '">';
                } else {
                    echo '<input type="text" id="field_' . htmlspecialchars($column) . '" name="fields[' . htmlspecialchars($column) . ']" value="' . htmlspecialchars($value) . '">';
                }
            }
            ?></div>
        <?php endforeach; ?>
        <button type="submit">Save Changes</button>
        <a href="database.php?table=<?php echo urlencode($table); ?>" style="margin-left: 1rem;">Back to Table</a>
    </form>
</div>

<?php admin_footer(); ?>