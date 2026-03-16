<?php
require_once __DIR__ . '/_ui.php';

$msg = '';
$msgClass = 'ok';
$ingestor = new KnowledgeIngestor();
$kb = new KnowledgeBase();
$train = new TrainingData();
$uploadDir = dirname(__DIR__) . '/storage/training/uploads';

$q = trim($_GET['q'] ?? '');
$pdo = Db::pdo();
$recentTraining = [];
if ($pdo) {
    if ($q !== '') {
        $stmt = $pdo->prepare('SELECT * FROM training_data WHERE input_text LIKE :q OR expected_intent LIKE :q ORDER BY created_at DESC LIMIT 200');
        $stmt->execute(['q' => "%$q%"]);
        $recentTraining = $stmt->fetchAll();
    } else {
        $recentTraining = $train->all(20);
    }
}

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'upload_file' && isset($_FILES['training_file'])) {
        $name = basename((string) $_FILES['training_file']['name']);
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, ['json', 'txt', 'xml'], true)) {
            $msg = 'Only json/txt/xml allowed.';
            $msgClass = 'err';
        } else {
            $target = $uploadDir . '/' . date('Ymd_His_') . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $name);
            if (move_uploaded_file($_FILES['training_file']['tmp_name'], $target)) {
                $result = $ingestor->ingestFile($target, 'admin_upload');
                $msg = $result['message'] . ' | rows: ' . $result['count'];
                $msgClass = $result['ok'] ? 'ok' : 'err';
            } else {
                $msg = 'Upload failed.';
                $msgClass = 'err';
            }
        }
    }

    if ($action === 'add_qa') {
        $q = trim((string) ($_POST['question'] ?? ''));
        $a = trim((string) ($_POST['answer'] ?? ''));
        $intent = trim((string) ($_POST['intent'] ?? 'general'));
        $lang = trim((string) ($_POST['language'] ?? 'bilingual'));
        if ($q === '' || $a === '') {
            $msg = 'Question and answer are required.';
            $msgClass = 'err';
        } else {
            $kb->upsertQA($q, $a, $lang);
            $train->add($q, $intent, $lang, 0.9, 'admin_manual');
            $msg = 'Manual training entry saved.';
            $msgClass = 'ok';
        }
    }

    if ($action === 'add_response_policy') {
        $trigger = trim((string) ($_POST['trigger_text'] ?? ''));
        $response = trim((string) ($_POST['policy_response'] ?? ''));
        $lang = trim((string) ($_POST['policy_language'] ?? 'bilingual'));
        $mode = trim((string) ($_POST['match_mode'] ?? 'contains'));
        if ($trigger === '' || $response === '') {
            $msg = 'Trigger and response are required for policy training.';
            $msgClass = 'err';
        } else {
            $kb->upsertResponsePolicy($trigger, $response, $lang, $mode);
            $train->add($trigger, 'custom_policy', $lang, 0.95, 'admin_policy');
            $msg = 'Custom response policy saved.';
            $msgClass = 'ok';
        }
    }

    if ($action === 'load_seed_files') {
        $base = dirname(__DIR__) . '/storage/training';
        $files = [$base . '/seed_knowledge.json', $base . '/seed_knowledge.txt', $base . '/seed_knowledge.xml'];
        $total = 0;
        foreach ($files as $f) {
            if (is_file($f)) {
                $res = $ingestor->ingestFile($f, 'seed_file');
                $total += (int) $res['count'];
            }
        }
        $msg = 'Seed files processed. Rows added: ' . $total;
        $msgClass = 'ok';
    }

    if ($action === 'import_download_ai_folder') {
        $folder = dirname(__DIR__) . '/downloadaitrainfile';
        if (!is_dir($folder)) {
            $msg = 'downloadaitrainfile folder not found.';
            $msgClass = 'err';
        } else {
            $codingLimit = (int) ($_POST['coding_limit'] ?? 1000);
            $squadLimit = (int) ($_POST['squad_limit'] ?? 5000);
            $newsLimit = (int) ($_POST['news_limit'] ?? 1000);
            $importer = new DownloadedDatasetImporter();
            $stats = $importer->importFolder($folder, [
                'coding' => max(0, $codingLimit),
                'squad' => max(0, $squadLimit),
                'news' => max(0, $newsLimit),
            ]);
            $msg = sprintf(
                'Imported: coding=%d, squad=%d, news=%d',
                $stats['coding'],
                $stats['squad'],
                $stats['news']
            );
            if (!empty($stats['errors'])) {
                $msg .= ' | errors: ' . implode('; ', $stats['errors']);
            }
            $msgClass = 'ok';
        }
    }

    if ($action === 'import_system_bundle') {
        $importer = new SystemSeedImporter();
        if (isset($_FILES['bundle_file']) && is_uploaded_file($_FILES['bundle_file']['tmp_name'])) {
            $bundlePath = $uploadDir . '/' . date('Ymd_His_') . '_bundle.json';
            move_uploaded_file($_FILES['bundle_file']['tmp_name'], $bundlePath);
            $res = $importer->importBundle($bundlePath, 'admin_bundle_upload');
            $msg = $res['message'] . (isset($res['counts']) ? ' | ' . json_encode($res['counts']) : '');
            $msgClass = $res['ok'] ? 'ok' : 'err';
        } else {
            $defaultBundle = dirname(__DIR__) . '/storage/training/full_system_seed_bundle.json';
            $res = $importer->importBundle($defaultBundle, 'admin_bundle_default');
            $msg = $res['message'] . (isset($res['counts']) ? ' | ' . json_encode($res['counts']) : '');
            $msgClass = $res['ok'] ? 'ok' : 'err';
        }
    }

    if ($action === 'build_json_training_pack') {
        $sourceRoot = dirname(__DIR__) . '/downloadaitrainfile';
        $outputDir = dirname(__DIR__) . '/storage/training/json_pack';
        $builder = new JsonTrainingPackBuilder();
        $res = $builder->build($sourceRoot, $outputDir);
        $msg = $res['message'] . (isset($res['counts']) ? ' | ' . json_encode($res['counts']) : '');
        $msgClass = $res['ok'] ? 'ok' : 'err';
    }

    if ($action === 'import_json_training_pack') {
        $packDir = dirname(__DIR__) . '/storage/training/json_pack';
        $importer = new JsonTrainingPackImporter();
        $res = $importer->import($packDir, 'admin_json_pack');
        $msg = $res['message'] . (isset($res['counts']) ? ' | ' . json_encode($res['counts']) : '');
        $msgClass = $res['ok'] ? 'ok' : 'err';
    }

    if ($action === 'build_and_import_json_pack') {
        $sourceRoot = dirname(__DIR__) . '/downloadaitrainfile';
        $outputDir = dirname(__DIR__) . '/storage/training/json_pack';
        $builder = new JsonTrainingPackBuilder();
        $buildRes = $builder->build($sourceRoot, $outputDir);
        if (!($buildRes['ok'] ?? false)) {
            $msg = 'Build failed: ' . ($buildRes['message'] ?? 'unknown error');
            $msgClass = 'err';
        } else {
            $importer = new JsonTrainingPackImporter();
            $importRes = $importer->import($outputDir, 'admin_build_and_import');
            if (!($importRes['ok'] ?? false)) {
                $msg = 'Build done, import failed: ' . ($importRes['message'] ?? 'unknown error');
                $msgClass = 'err';
            } else {
                $msg = 'Build+Import done | build=' . json_encode($buildRes['counts'] ?? []) . ' | import=' . json_encode($importRes['counts'] ?? []);
                $msgClass = 'ok';
            }
        }
    }
}

$recentTraining = $train->all(20);

admin_header('Training');
?>
<div class="card">
    <h2>Training Manager</h2>
    <p class="muted">Upload dataset files, add manual QA pairs, and load bundled seed data.</p>
    <?php if ($msg): ?><div class="<?php echo $msgClass; ?>"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
</div>

<div class="card">
    <h3>Upload Training File</h3>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="upload_file">
        <div class="row">
            <input type="file" name="training_file" required>
            <button type="submit">Upload + Ingest</button>
        </div>
    </form>
</div>

<div class="card">
    <h3>Manual QA Training</h3>
    <form method="post">
        <input type="hidden" name="action" value="add_qa">
        <div class="row">
            <input type="text" name="question" placeholder="Question" required>
            <input type="text" name="answer" placeholder="Answer" required>
        </div>
        <div class="row" style="margin-top:.6rem">
            <input type="text" name="intent" value="general" placeholder="Intent">
            <select name="language">
                <option value="bilingual">Bilingual</option>
                <option value="en">English</option>
                <option value="hi">Hindi</option>
            </select>
        </div>
        <button style="margin-top:.7rem" type="submit">Save QA</button>
    </form>
</div>

<div class="card">
    <h3>Custom Reply Training (Kis Baat Ka Kya Jawab)</h3>
    <form method="post">
        <input type="hidden" name="action" value="add_response_policy">
        <div class="row">
            <input type="text" name="trigger_text" placeholder="Trigger text (example: discount policy)" required>
            <input type="text" name="policy_response" placeholder="Exact reply the AI should give" required>
        </div>
        <div class="row" style="margin-top:.6rem">
            <select name="match_mode">
                <option value="contains">Contains Match</option>
                <option value="exact">Exact Match</option>
            </select>
            <select name="policy_language">
                <option value="bilingual">Bilingual</option>
                <option value="en">English</option>
                <option value="hi">Hindi</option>
            </select>
        </div>
        <button style="margin-top:.7rem" type="submit">Save Custom Reply</button>
    </form>
</div>

<div class="card">
    <h3>Seed Data</h3>
    <form method="post">
        <input type="hidden" name="action" value="load_seed_files">
        <button type="submit">Load Bundled JSON/TXT/XML Seed</button>
    </form>
</div>

<div class="card">
    <h3>Import Downloaded AI Folder</h3>
    <p class="muted">Source: <code>downloadaitrainfile/</code> (SQuAD, coding dataset, 20news)</p>
    <form method="post">
        <input type="hidden" name="action" value="import_download_ai_folder">
        <div class="row">
            <input type="number" name="coding_limit" value="1000" min="0" placeholder="Coding rows limit">
            <input type="number" name="squad_limit" value="5000" min="0" placeholder="SQuAD rows limit">
        </div>
        <div class="row" style="margin-top:.6rem">
            <input type="number" name="news_limit" value="1000" min="0" placeholder="20news rows limit">
            <button type="submit">Import Folder Data</button>
        </div>
    </form>
</div>

<div class="card">
    <h3>Full System Seed Bundle (All Core Tables)</h3>
    <p class="muted">Imports data into training_data, patterns, neural_weights, knowledge_entities, knowledge_relations.</p>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="import_system_bundle">
        <div class="row">
            <input type="file" name="bundle_file" accept=".json">
            <button type="submit">Import Bundle</button>
        </div>
    </form>
</div>

<div class="card">
    <h3>JSON Pack Builder + Importer</h3>
    <p class="muted">Source: <code>downloadaitrainfile/</code> -> Output: <code>storage/training/json_pack/</code></p>
    <form method="post"
    i   <button type="submit">Build & Import Pack</button>
    </form>
</div>

    <h3>Recent Training Data</h3>
    <table>
        <tr><th>ID</th><th>Input Text</th><th>Intent</th><th>Language</th><th>Source</th><th>Time</th></tr>
        <?php foreach ($recentTraining as $row): ?>
            <tr>
                <td><?php echo (int) $row['id']; ?></td>
                <td><?php echo htmlspecialchars(mb_substr($row['input_text'], 0, 80)); ?></td>
                <td><?php echo htmlspecialchars($row['expected_intent']); ?></td>
                <td><?php echo htmlspecialchars($row['language_tag']); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['source'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php admin_footer(); ?>
