<?php
require_once __DIR__ . '/_ui.php';

$convModel = new Conversation();
$trainModel = new TrainingData();
$patternModel = new Patterns();
$kbModel = new KnowledgeBase();
$recentEntities = $kbModel->recentEntities(5);
$pdo = Db::pdo();

$convStats = $convModel->stats();
$trainStats = $trainModel->stats();
$patternStats = $patternModel->stats();
$kbStats = $kbModel->stats();
$recent = $convModel->recent(10);

$intentRows = [];
if ($pdo) {
    $intentRows = $pdo->query(
        'SELECT intent, COUNT(*) as total FROM conversations GROUP BY intent ORDER BY total DESC LIMIT 10'
    )->fetchAll();
}
$maxIntentCount = 0;
if (!empty($intentRows)) {
    $maxIntentCount = max(array_column($intentRows, 'total'));
}


admin_header('Dashboard');
?>
<div class="card">
    <h2>System Overview</h2>
    <p class="muted">Persistent AI engine with bilingual chat, memory store, and dataset ingestion.</p>
</div>

<div class="grid">
    <div class="card"><div class="muted">Conversations</div><div class="kpi"><?php echo $convStats['total']; ?></div></div>
    <div class="card"><div class="muted">Training Rows</div><div class="kpi"><?php echo $trainStats['total']; ?></div></div>
    <div class="card"><div class="muted">Knowledge Entities</div><div class="kpi"><?php echo $kbStats['total']; ?></div></div>
    <div class="card"><div class="muted">Pattern Tokens</div><div class="kpi"><?php echo $patternStats['total']; ?></div></div>
    <div class="card"><div class="muted">QA Entries</div><div class="kpi"><?php echo $kbStats['qa']; ?></div></div>
    <div class="card"><div class="muted">User Facts</div><div class="kpi"><?php echo $kbStats['facts']; ?></div></div>
    <div class="card"><div class="muted">Custom Policies</div><div class="kpi"><?php echo $kbStats['policies'] ?? 0; ?></div></div>
</div>

<?php if (!empty($intentRows)): ?>
<div class="card">
    <h3>Most Used Intents</h3>
    <div class="barchart">
        <?php foreach ($intentRows as $row): ?>
        <div class="barchart-item">
            <div class="barchart-label"><?php echo htmlspecialchars($row['intent']); ?></div>
            <div class="barchart-bar-container">
                <div class="barchart-bar" style="width: <?php echo $maxIntentCount > 0 ? (($row['total'] / $maxIntentCount) * 100) : 0; ?>%;"></div>
            </div>
            <div class="barchart-value"><?php echo (int) $row['total']; ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <h3>Recent Conversations</h3>
    <table>
        <tr><th>ID</th><th>User Input</th><th>Intent</th><th>Confidence</th><th>Time</th></tr>
        <?php foreach ($recent as $row): ?>
            <tr>
                <td><?php echo (int) $row['id']; ?></td>
                <td><?php echo htmlspecialchars(mb_substr($row['user_input'], 0, 70)); ?></td>
                <td><?php echo htmlspecialchars($row['intent']); ?></td>
                <td><?php echo htmlspecialchars((string) $row['confidence']); ?></td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="card">
    <h3>Recent Knowledge Entities</h3>
    <table>
        <tr><th>ID</th><th>Name</th><th>Type</th><th>Updated</th></tr>
        <?php foreach ($recentEntities as $row): ?>
            <tr>
                <td><?php echo (int) $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['type']); ?></td>
                <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php admin_footer(); ?>
