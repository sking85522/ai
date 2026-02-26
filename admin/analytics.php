<?php
require_once __DIR__ . '/_ui.php';

$pdo = Db::pdo();
$intentRows = [];
$langRows = [];
$dailyRows = [];

if ($pdo) {
    $intentRows = $pdo->query(
        'SELECT intent, COUNT(*) as total
         FROM conversations
         GROUP BY intent
         ORDER BY total DESC
         LIMIT 20'
    )->fetchAll();

    $langRows = $pdo->query(
        'SELECT input_language, response_language, COUNT(*) as total
         FROM conversations
         GROUP BY input_language, response_language
         ORDER BY total DESC'
    )->fetchAll();

    $dailyRows = $pdo->query(
        'SELECT DATE(created_at) as day, COUNT(*) as total
         FROM conversations
         WHERE created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
         GROUP BY DATE(created_at)
         ORDER BY day DESC'
    )->fetchAll();
}

admin_header('Analytics');
?>
<div class="card">
    <h2>AI Analytics</h2>
    <p class="muted">Intent distribution, language routing and daily usage.</p>
</div>

<div class="card">
    <h3>Intent Distribution</h3>
    <table>
        <tr><th>Intent</th><th>Total</th></tr>
        <?php foreach ($intentRows as $row): ?>
            <tr><td><?php echo htmlspecialchars($row['intent']); ?></td><td><?php echo (int) $row['total']; ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="card">
    <h3>Language Routing</h3>
    <table>
        <tr><th>Input Language</th><th>Response Language</th><th>Total</th></tr>
        <?php foreach ($langRows as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['input_language']); ?></td>
                <td><?php echo htmlspecialchars($row['response_language']); ?></td>
                <td><?php echo (int) $row['total']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="card">
    <h3>Daily Conversations (Last 14 Days)</h3>
    <table>
        <tr><th>Date</th><th>Total</th></tr>
        <?php foreach ($dailyRows as $row): ?>
            <tr><td><?php echo htmlspecialchars($row['day']); ?></td><td><?php echo (int) $row['total']; ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>
<?php admin_footer(); ?>
