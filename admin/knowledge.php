<?php
require_once __DIR__ . '/_ui.php';

$msg = '';
$msgClass = 'ok';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $entityId = (int) ($_POST['entity_id'] ?? 0);

    if ($entityId > 0) {
        if ($action === 'edit_entity') {
            header("Location: knowledge_edit.php?id=$entityId");
            exit;
        }
        if ($action === 'delete_entity') {
            $kb = new KnowledgeBase();
            $success = $kb->deleteEntity($entityId);
            if ($success) {
                $msg = "Entity ID $entityId deleted successfully.";
                $msgClass = 'ok';
            } else {
                $msg = "Failed to delete Entity ID $entityId.";
                $msgClass = 'err';
            }
        }
    }
}

$q = trim($_GET['q'] ?? '');
$results = [];
$pdo = Db::pdo();

if ($q !== '' && $pdo) {
    $stmt = $pdo->prepare(
        'SELECT * FROM knowledge_entities 
         WHERE name LIKE :query OR metadata LIKE :query 
         ORDER BY updated_at DESC 
         LIMIT 100'
    );
    $stmt->execute(['query' => '%' . $q . '%']);
    $results = $stmt->fetchAll();
}

admin_header('Knowledge Base');
?>
<div class="card">
    <h2>Knowledge Base Search</h2>
    <p class="muted">Search for entities, Q&A pairs, and other facts stored in the knowledge graph.</p>
</div>
 <?php if ($msg): ?><div class="<?php echo $msgClass; ?>"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>

<div class="card">
    <form method="get" action="knowledge.php">
        <div class="row">
            <input type="search" name="q" placeholder="Search by name or metadata..." value="<?php echo htmlspecialchars($q); ?>">
            <button type="submit">Search</button>
        </div>
    </form>
</div>

<?php if ($q !== ''): ?>
<div class="card">
    <h3>Search Results for "<?php echo htmlspecialchars($q); ?>"</h3>
    <?php if (empty($results)): ?>
        <p>No results found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>ID</th><th>Name</th><th>Type</th><th>Lang</th><th style="width:40%;">Metadata</th><th>Actions</th><th>Updated</th></tr>
            </thead>
            <tbody>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?php echo (int) $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                    <td><?php echo htmlspecialchars($row['language_tag']); ?></td>
                    <td><pre style="font-size: .8rem; white-space: pre-wrap; word-break: break-all; margin:0;"><?php 
                        $meta = json_decode($row['metadata']);
                        echo htmlspecialchars(json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); 
                    ?></pre></td>
                    <td style="white-space: nowrap;">
                        <form method="post" action="knowledge.php?q=<?php echo urlencode($q); ?>" style="display:inline-block">
                            <input type="hidden" name="action" value="edit_entity">
                            <input type="hidden" name="entity_id" value="<?php echo (int) $row['id']; ?>">
                            <button type="submit">Edit</button>
                        </form>
                        <form method="post" action="knowledge.php?q=<?php echo urlencode($q); ?>" style="display:inline-block">
                            <input type="hidden" name="action" value="delete_entity">
                            <input type="hidden" name="entity_id" value="<?php echo (int) $row['id']; ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this entity?')" style="background:#c62828">Delete</button>
                        </form>
                    </td>
                    <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php admin_footer(); ?>