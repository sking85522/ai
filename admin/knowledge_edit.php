<?php
require_once __DIR__ . '/_ui.php';

$msg = '';
$msgClass = 'ok';
$pdo = Db::pdo();
$entity = null;
$entityId = (int) ($_GET['id'] ?? 0);

if (!$pdo || $entityId <= 0) {
    admin_header('Error');
    echo '<div class="card err">Invalid request or database connection failed.</div>';
    admin_footer();
    exit;
}

// Fetch entity
$stmt = $pdo->prepare('SELECT * FROM knowledge_entities WHERE id = ?');
$stmt->execute([$entityId]);
$entity = $stmt->fetch();

// Fetch relations
$relations = [];
$relationStmt = $pdo->prepare(
    'SELECT r.id, r.relation, e_source.name as source_name, e_target.name as target_name
     FROM knowledge_relations r
     JOIN knowledge_entities e_source ON r.source_entity_id = e_source.id
     JOIN knowledge_entities e_target ON r.target_entity_id = e_target.id
     WHERE r.source_entity_id = ? OR r.target_entity_id = ?'
);
$relationStmt->execute([$entityId, $entityId]);
$relations = $relationStmt->fetchAll();

// Fetch all other entities for dropdown
$allEntities = $pdo->query('SELECT id, name FROM knowledge_entities WHERE id != ' . $entityId . ' ORDER BY name ASC')->fetchAll();

if (!$entity) {
    admin_header('Error');
    echo '<div class="card err">Entity not found.</div>';
    admin_footer();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_relation') {
        $otherEntityId = (int)($_POST['other_entity_id'] ?? 0);
        $relationName = trim((string)($_POST['relation_name'] ?? ''));
        $direction = $_POST['direction'] ?? 'source_is_current';

        if ($otherEntityId > 0 && $relationName !== '') {
            $sourceId = ($direction === 'source_is_current') ? $entityId : $otherEntityId;
            $targetId = ($direction === 'source_is_current') ? $otherEntityId : $entityId;

            $insertStmt = $pdo->prepare('INSERT INTO knowledge_relations (source_entity_id, relation, target_entity_id, relation_weight) VALUES (?, ?, ?, 1.0)');
            if ($insertStmt->execute([$sourceId, $relationName, $targetId])) {
                $msg = 'Relation added successfully.';
                $msgClass = 'ok';
            } else {
                $msg = 'Failed to add relation. It might already exist.';
                $msgClass = 'err';
            }
        } else {
            $msg = 'Relation name and target entity are required.';
            $msgClass = 'err';
        }
    }

    if ($action === 'delete_relation') {
        $relationId = (int)($_POST['relation_id'] ?? 0);
        if ($relationId > 0) {
            $deleteStmt = $pdo->prepare('DELETE FROM knowledge_relations WHERE id = ?');
            if ($deleteStmt->execute([$relationId])) {
                $msg = 'Relation deleted successfully.';
                $msgClass = 'ok';
            } else {
                $msg = 'Failed to delete relation.';
                $msgClass = 'err';
            }
        }
    }

    $name = trim((string) ($_POST['name'] ?? ''));
    $type = trim((string) ($_POST['type'] ?? ''));
    $lang = trim((string) ($_POST['language_tag'] ?? ''));
    $metaJson = trim((string) ($_POST['metadata'] ?? '{}'));

    $meta = json_decode($metaJson, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $msg = 'Invalid JSON in metadata: ' . json_last_error_msg();
        $msgClass = 'err';
    } elseif ($name === '') {
        $msg = 'Name cannot be empty.';
        $msgClass = 'err';
    } else {
        $updateStmt = $pdo->prepare(
            'UPDATE knowledge_entities SET name = ?, type = ?, language_tag = ?, metadata = ?, updated_at = NOW() WHERE id = ?'
        );
        if ($updateStmt->execute([$name, $type, $lang, json_encode($meta), $entityId])) {
            $msg = 'Entity updated successfully.';
            $msgClass = 'ok';
            // Re-fetch to show updated data
            $stmt->execute([$entityId]);
            $entity = $stmt->fetch();
        } else {
            $msg = 'Failed to update entity.';
            $msgClass = 'err';
        }
    }

    // Re-fetch relations after any change
    $relationStmt->execute([$entityId, $entityId]);
    $relations = $relationStmt->fetchAll();
}

admin_header('Edit Knowledge Entity');
?>
<div class="card">
    <h2>Edit Entity #<?php echo (int) $entity['id']; ?></h2>
    <p class="muted">Modify the details of this knowledge base entry.</p>
    <?php if ($msg): ?><div class="card <?php echo $msgClass; ?>"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
</div>

<div class="card">
    <form method="post" action="knowledge_edit.php?id=<?php echo (int) $entity['id']; ?>">
        <input type="hidden" name="action" value="update_entity">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($entity['name']); ?>" required style="margin-bottom: 1rem;">
        <div class="row"><label for="type">Type</label><label for="language_tag">Language</label></div>
        <div class="row" style="margin-bottom: 1rem;"><input type="text" id="type" name="type" value="<?php echo htmlspecialchars($entity['type']); ?>"><input type="text" id="language_tag" name="language_tag" value="<?php echo htmlspecialchars($entity['language_tag']); ?>"></div>
        <label for="metadata">Metadata (JSON)</label>
        <textarea id="metadata" name="metadata" rows="10" style="font-family: monospace; margin-bottom: 1rem;"><?php $meta = json_decode($entity['metadata']); echo htmlspecialchars(json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></textarea>
        <button type="submit">Save Changes</button>
        <a href="knowledge.php?q=<?php echo urlencode($entity['name']); ?>" style="margin-left: 1rem; text-decoration: none;">Back to Search</a>
    </form>
</div>

<div class="card">
    <h3>Relations</h3>
    <?php if (empty($relations)): ?>
        <p class="muted">No relations found for this entity.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>Source</th><th>Relation</th><th>Target</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($relations as $rel): ?>
                <tr>
                    <td><?php echo htmlspecialchars($rel['source_name']); ?></td>
                    <td><strong><?php echo htmlspecialchars($rel['relation']); ?></strong></td>
                    <td><?php echo htmlspecialchars($rel['target_name']); ?></td>
                    <td>
                        <form method="post" action="knowledge_edit.php?id=<?php echo $entityId; ?>">
                            <input type="hidden" name="action" value="delete_relation">
                            <input type="hidden" name="relation_id" value="<?php echo (int)$rel['id']; ?>">
                            <button type="submit" style="background:#c62828; padding: .3rem .6rem;" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h4 style="margin-top: 2rem; margin-bottom: 1rem;">Add New Relation</h4>
    <form method="post" action="knowledge_edit.php?id=<?php echo $entityId; ?>">
        <input type="hidden" name="action" value="add_relation">
        <div style="display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label>This Entity (<?php echo htmlspecialchars($entity['name']); ?>)</label>
                <select name="direction"><option value="source_is_current">is the Source</option><option value="target_is_current">is the Target</option></select>
            </div>
            <div>
                <label>Relation Name</label>
                <input type="text" name="relation_name" placeholder="e.g., is_a, works_with" required>
            </div>
            <div>
                <label>Other Entity</label>
                <select name="other_entity_id" required><option value="">-- Choose Entity --</option><?php foreach ($allEntities as $e) { echo '<option value="' . $e['id'] . '">' . htmlspecialchars($e['name']) . '</option>'; } ?></select>
            </div>
        </div>
        <button type="submit">Add Relation</button>
    </form>
</div>

<?php admin_footer(); ?>