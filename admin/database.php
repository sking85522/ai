<?php
require_once __DIR__ . '/_ui.php';

$pdo = Db::pdo();
if (!$pdo) {
    admin_header('Database Error');
    echo '<div class="card err">Database connection failed.</div>';
    admin_footer();
    exit;
}

$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
$viewTable = $_GET['table'] ?? '';
$query = trim($_POST['query'] ?? '');
$queryResult = null;
$queryError = null;

$tableSchema = [];
$tableData = [];

if ($viewTable && in_array($viewTable, $tables)) {
    try {
        $tableSchema = $pdo->query("DESCRIBE `$viewTable`")->fetchAll(PDO::FETCH_ASSOC);
        $tableData = $pdo->query("SELECT * FROM `$viewTable` LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $queryError = $e->getMessage();
    }
} elseif ($query) {
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        if (stripos($query, 'SELECT') === 0 || stripos($query, 'SHOW') === 0 || stripos($query, 'DESC') === 0) {
            $queryResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $queryResult = ['affected_rows' => $stmt->rowCount()];
        }
    } catch (PDOException $e) {
        $queryError = $e->getMessage();
    }
}

admin_header('Database Manager');
?>
<div class="layout" style="grid-template-columns: 200px 1fr;">
    <aside class="nav" style="height: 100vh; position: sticky; top: 0;">
        <div class="brand">Tables</div>
        <?php foreach ($tables as $table): ?>
            <a href="?table=<?php echo htmlspecialchars($table); ?>" class="<?php echo $viewTable === $table ? 'active' : ''; ?>"><?php echo htmlspecialchars($table); ?></a>
        <?php endforeach; ?>
    </aside>
    <main class="main">
        <div class="card">
            <h2>Database Manager</h2>
            <p class="muted">View table schemas, browse data, and run raw SQL queries.</p>
        </div>

        <div class="card">
            <h3>SQL Runner</h3>
            <div class="card err" style="margin-bottom: 1rem;"><b>Warning:</b> Running queries directly can corrupt your database. Use with extreme caution. No rollbacks are available.</div>
            <form method="post">
                <textarea name="query" rows="4" placeholder="SELECT * FROM conversations LIMIT 10;"><?php echo htmlspecialchars($query); ?></textarea>
                <button type="submit" style="margin-top: .7rem;">Run Query</button>
            </form>
        </div>

        <?php if ($queryError): ?>
            <div class="card err"><b>Query Error:</b><br><pre><?php echo htmlspecialchars($queryError); ?></pre></div>
        <?php endif; ?>

        <?php if (is_array($queryResult)): ?>
            <div class="card"><h3>Query Result</h3><?php echo render_table($queryResult); ?></div>
        <?php endif; ?>

        <?php if ($viewTable): ?>
            <div class="card"><h3>Schema: `<?php echo htmlspecialchars($viewTable); ?>`</h3><?php echo render_table($tableSchema); ?></div>
            <div class="card"><h3>Data: `<?php echo htmlspecialchars($viewTable); ?>` (First 50 rows)</h3><?php echo render_table($tableData); ?></div>
        <?php endif; ?>
    </main>
</div>

<?php
function render_table(array $data): string {
    if (empty($data)) return '<p class="muted">No data to display.</p>';
    $headers = array_keys($data[0]);
    $html = '<table><thead><tr>';
    foreach ($headers as $h) $html .= '<th>' . htmlspecialchars($h) . '</th>';
    $html .= '</tr></thead><tbody>';
    foreach ($data as $row) {
        $html .= '<tr>';
        foreach ($row as $val) {
            $displayVal = is_string($val) ? htmlspecialchars(mb_substr($val, 0, 150)) : htmlspecialchars((string)$val);
            $html .= '<td>' . ($displayVal ?: '&nbsp;') . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

// We are overriding the default layout, so we need a custom footer
echo '</body></html>';
?>