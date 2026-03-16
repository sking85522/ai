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
$diagram = "erDiagram\n";
$dbName = $pdo->query('SELECT database()')->fetchColumn();

foreach ($tables as $table) {
    $columns = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
    $diagram .= "    $table {\n";
    foreach ($columns as $col) {
        $type = preg_replace('/[^a-zA-Z0-9]/', '_', $col['Type']);
        $field = $col['Field'];
        $key = $col['Key'] === 'PRI' ? 'PK' : ($col['Key'] === 'MUL' ? 'FK' : '');
        $diagram .= "        $type $field $key\n";
    }
    $diagram .= "    }\n";
}

// Add relationships based on foreign keys
foreach ($tables as $table) {
    $fkStmt = $pdo->prepare("
        SELECT
            kcu.column_name,
            kcu.referenced_table_name
        FROM
            information_schema.key_column_usage AS kcu
        JOIN
            information_schema.table_constraints AS tc ON kcu.constraint_name = tc.constraint_name AND kcu.table_schema = tc.table_schema
        WHERE
            kcu.table_schema = :db_name AND kcu.table_name = :table_name AND tc.constraint_type = 'FOREIGN KEY'
    ");
    $fkStmt->execute(['db_name' => $dbName, 'table_name' => $table]);
    $foreignKeys = $fkStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($foreignKeys as $fk) {
        $diagram .= "    {$table} }o--|| {$fk['referenced_table_name']} : \"{$fk['column_name']}\"\n";
    }
}

admin_header('Database Schema Diagram');
?>
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Database Schema</h2>
        <div>
            <button id="export-png" class="button" style="background-color: #00796b;">Export as PNG</button>
            <a href="database.php" class="button">Back to Manager</a>
        </div>
    </div>
    <p class="muted">Visual representation of tables and columns.</p>
    
    <div class="mermaid" style="background: white; padding: 20px; border-radius: 8px; overflow: auto;">
        <?php echo htmlspecialchars($diagram); ?>
    </div>
</div>

<script type="module">
    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
    mermaid.initialize({ startOnLoad: false, theme: 'base', themeVariables: { 'background': '#ffffff' } });
    await mermaid.run();

    document.getElementById('export-png').addEventListener('click', async () => {
        const svgElement = document.querySelector('.mermaid svg');
        if (!svgElement) { return alert('Diagram not rendered yet.'); }

        const svgData = new XMLSerializer().serializeToString(svgElement);
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const margin = 20;
        const svgBBox = svgElement.getBBox();
        canvas.width = svgBBox.width + margin * 2;
        canvas.height = svgBBox.height + margin * 2;
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        const img = new Image();
        img.onload = () => {
            ctx.drawImage(img, margin, margin);
            const link = document.createElement('a');
            link.download = 'database-schema.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        };
        img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
    });
</script>

<?php admin_footer(); ?>