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
$msg = '';
$msgClass = 'ok';

// Basic validation
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
if ($table === '' || !in_array($table, $tables)) {
    admin_header('Error');
    echo '<div class="card err">Invalid request. Table name is missing or invalid.</div>';
    admin_footer();
    exit;
}

// Fetch schema
$schemaResult = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
$colTypes = [];
$autoIncrementCol = null;
foreach ($schemaResult as $col) {
    $colTypes[$col['Field']] = strtolower($col['Type']);
    if (str_contains($col['Extra'], 'auto_increment')) {
        $autoIncrementCol = $col['Field'];
    }
}
$schema = array_keys($colTypes);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $insertValues = $_POST['fields'] ?? [];
    $colsToInsert = [];
    $placeholders = [];
    $params = [];

    foreach ($insertValues as $col => $val) {
        if (in_array($col, $schema) && $col !== $autoIncrementCol) {
            $colsToInsert[] = "`$col`";
            $placeholders[] = '?';
            $params[] = ($val === '') ? null : $val; // Allow inserting NULLs for empty strings
        }
    }

    if (!empty($colsToInsert)) {
        $sql = "INSERT INTO `$table` (" . implode(', ', $colsToInsert) . ") VALUES (" . implode(', ', $placeholders) . ")";
        try {
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($params)) {
                $lastId = $pdo->lastInsertId();
                header("Location: database.php?table=" . urlencode($table) . "&msg=Row+created" . ($lastId != 0 ? "+with+ID+$lastId" : ""));
                exit;
            } else {
                $msg = 'Failed to create row. Error: ' . implode(' ', $stmt->errorInfo());
                $msgClass = 'err';
            }
        } catch (PDOException $e) {
            $msg = 'Error creating row: ' . $e->getMessage();
            $msgClass = 'err';
        }
    }
}

admin_header("Create Row in $table");
?>
<div class="card">
    <h2>Create New Row in <code><?php echo htmlspecialchars($table); ?></code></h2>
    <p class="muted">Fill in the fields and click save to add a new entry. Auto-increment fields are omitted.</p>
    <?php if ($msg): ?><div class="card <?php echo $msgClass; ?>"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
</div>

<div class="card">
    <form method="post">
        <?php foreach ($schema as $column): ?>
            <?php if ($column === $autoIncrementCol) continue; ?>
            <div style="margin-bottom: 1rem;">
                <label for="field_<?php echo htmlspecialchars($column); ?>"><strong><?php echo htmlspecialchars($column); ?></strong> <small>(<?php echo htmlspecialchars($colTypes[$column]); ?>)</small></label>
                <?php
                    $type = $colTypes[$column] ?? 'text';
                    $value = ''; // Default empty value for create form
                    if (str_contains($type, 'text') || str_contains($type, 'blob') || str_contains($type, 'json')) {
                        echo '<textarea id="field_' . htmlspecialchars($column) . '" name="fields[' . htmlspecialchars($column) . ']" rows="3">' . htmlspecialchars($value) . '</textarea>';
                    } elseif (str_contains($type, 'int') || str_contains($type, 'decimal') || str_contains($type, 'float') || str_contains($type, 'double')) {
                        echo '<input type="number" step="any" id="field_' . htmlspecialchars($column) . '" name="fields[' . htmlspecialchars($column) . ']" value="' . htmlspecialchars($value) . '">';
                    } elseif (str_contains($type, 'datetime') || str_contains($type, 'timestamp')) {
                        echo '<input type="datetime-local" id="field_' . htmlspecialchars($column) . '" name="fields[' . htmlspecialchars($column) . ']" value="' . htmlspecialchars($value) . '">';
                    } elseif (str_contains($type, 'date')) {
                        echo '<input type="date" id="field_' . htmlspecialchars($column) . '" name="fields[' . htmlspecialchars($column) . ']" value="' . htmlspecialchars($value) . '">';
                    } else {
                        echo '<input type="text" id="field_' . htmlspecialchars($column) . '" name="fields[' . htmlspecialchars($column) . ']" value="' . htmlspecialchars($value) . '">';
                    }
                ?>
            </div>
        <?php endforeach; ?>
        <button type="submit">Save New Row</button>
        <a href="database.php?table=<?php echo urlencode($table); ?>" style="margin-left: 1rem;">Cancel</a>
    </form>
</div>

<?php admin_footer(); ?>