<?php
require_once __DIR__ . '/_ui.php';

$pdo = Db::pdo();
if (!$pdo) {
    admin_header('Database Error');
    echo '<div class="card err">Database connection failed.</div>';
    admin_footer();
    exit;
}

// Fetch tables with size information
$tables = [];
$tableSizes = [];
try {
    $status = $pdo->query('SHOW TABLE STATUS')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($status as $row) {
        $tables[] = $row['Name'];
        $tableSizes[$row['Name']] = ($row['Data_length'] ?? 0) + ($row['Index_length'] ?? 0);
    }
} catch (PDOException $e) {
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
}

$viewTable = $_GET['table'] ?? '';
$query = trim($_POST['query'] ?? '');
$queryResult = null;
$queryError = null;

// Handle Bulk Delete Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'bulk_delete') {
    $bulkTable = $_POST['table'] ?? '';
    $bulkPkName = $_POST['pk_name'] ?? '';
    $bulkIds = $_POST['selected_ids'] ?? [];

    if (in_array($bulkTable, $tables) && $bulkPkName && !empty($bulkIds)) {
        // Basic sanitization. This assumes numeric PKs but is safer.
        $sanitizedIds = array_filter($bulkIds, fn($id) => $id !== '');
        if (!empty($sanitizedIds)) {
            $placeholders = implode(',', array_fill(0, count($sanitizedIds), '?'));
            $sql = "DELETE FROM `$bulkTable` WHERE `$bulkPkName` IN ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($sanitizedIds);
            header("Location: ?table=" . urlencode($bulkTable) . "&msg=Selected+rows+deleted");
            exit;
        }
    }
    // Redirect if something was wrong to avoid resubmission
    header("Location: ?table=" . urlencode($bulkTable) . "&msg=Bulk+delete+failed");
    exit;
}

// Handle Inline Update Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_cell') {
    header('Content-Type: application/json');
    $uTable = $_POST['table'] ?? '';
    $uPkName = $_POST['pk_name'] ?? '';
    $uPkValue = $_POST['pk_value'] ?? '';
    $uCol = $_POST['column'] ?? '';
    $uVal = $_POST['value'] ?? '';

    if (in_array($uTable, $tables)) {
        $cols = $pdo->query("DESCRIBE `$uTable`")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array($uCol, $cols) && $uCol !== $uPkName) {
            try {
                $stmt = $pdo->prepare("UPDATE `$uTable` SET `$uCol` = ? WHERE `$uPkName` = ?");
                $stmt->execute([$uVal, $uPkValue]);
                echo json_encode(['ok' => true]);
            } catch (Exception $e) { echo json_encode(['ok' => false, 'error' => $e->getMessage()]); }
        } else { echo json_encode(['ok' => false, 'error' => 'Invalid column or PK']); }
    } else { echo json_encode(['ok' => false, 'error' => 'Invalid table']); }
    exit;
}

// Handle Duplicate Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'duplicate_row') {
    $dupTable = $_POST['table'] ?? '';
    $dupPkName = $_POST['pk_name'] ?? '';
    $dupPkValue = $_POST['pk_value'] ?? '';

    if (in_array($dupTable, $tables)) {
        try {
            $schemaResult = $pdo->query("DESCRIBE `$dupTable`")->fetchAll(PDO::FETCH_ASSOC);
            $autoIncrementCol = null;
            $columns = array_column($schemaResult, 'Field');
            foreach ($schemaResult as $col) {
                if (str_contains($col['Extra'], 'auto_increment')) {
                    $autoIncrementCol = $col['Field'];
                    break;
                }
            }

            if (in_array($dupPkName, $columns)) {
                $stmt = $pdo->prepare("SELECT * FROM `$dupTable` WHERE `$dupPkName` = ?");
                $stmt->execute([$dupPkValue]);
                if ($rowToCopy = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($autoIncrementCol) unset($rowToCopy[$autoIncrementCol]);
                    $colsToInsert = array_map(fn($c) => "`$c`", array_keys($rowToCopy));
                    $placeholders = array_fill(0, count($rowToCopy), '?');
                    $sql = "INSERT INTO `$dupTable` (" . implode(', ', $colsToInsert) . ") VALUES (" . implode(', ', $placeholders) . ")";
                    $pdo->prepare($sql)->execute(array_values($rowToCopy));
                    header("Location: ?table=" . urlencode($dupTable) . "&msg=Row+duplicated.+New+ID:+" . $pdo->lastInsertId());
                    exit;
                }
            }
        } catch (PDOException $e) {
            header("Location: ?table=" . urlencode($dupTable) . "&msg=Error+duplicating+row.");
            exit;
        }
    }
}

// Handle Delete Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_row') {
    $delTable = $_POST['table'] ?? '';
    $delPk = $_POST['pk_name'] ?? '';
    $delVal = $_POST['pk_value'] ?? '';
    if (in_array($delTable, $tables)) {
        $cols = $pdo->query("DESCRIBE `$delTable`")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array($delPk, $cols)) {
            $pdo->prepare("DELETE FROM `$delTable` WHERE `$delPk` = ?")->execute([$delVal]);
            header("Location: ?table=" . urlencode($delTable) . "&msg=Row+deleted");
            exit;
        }
    }
}

// Handle Optimize/Repair Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['action'] ?? '', ['optimize', 'repair', 'truncate'])) {
    $opTable = $_POST['table'] ?? '';
    $opAction = $_POST['action'];
    $opMode = strtoupper($opAction);
    
    if ($opTable === 'ALL') {
        $allTables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        if ($allTables) {
            $pdo->query("$opMode TABLE `" . implode('`, `', $allTables) . "`");
            header("Location: ?msg=All+tables+${opAction}ed");
            exit;
        }
    } elseif ($opAction === 'truncate' && in_array($opTable, $tables)) {
        $pdo->query("TRUNCATE TABLE `$opTable`");
        header("Location: ?table=" . urlencode($opTable) . "&msg=Table+truncated+(emptied)");
        exit;
    } elseif (in_array($opTable, $tables)) {
        $pdo->query("$opMode TABLE `$opTable`");
        header("Location: ?table=" . urlencode($opTable) . "&msg=Table+${opAction}ed");
        exit;
    }
}

$tableSchema = [];
$tableData = [];
$primaryKey = null;

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 50;
$offset = ($page - 1) * $limit;
$totalRows = 0;
$search = trim($_GET['search'] ?? '');
$sortBy = $_GET['sort_by'] ?? null;
$sortDir = strtolower($_GET['sort_dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';
$filterCol = trim($_GET['filter_col'] ?? '');
$filterVal = isset($_GET['filter_val']) ? (string)$_GET['filter_val'] : '';

if ($viewTable && in_array($viewTable, $tables)) {
    try {
        $tableSchema = $pdo->query("DESCRIBE `$viewTable`")->fetchAll(PDO::FETCH_ASSOC);

        // Find primary key (only supports simple, single-column PKs for now)
        $keyStmt = $pdo->query("SHOW KEYS FROM `$viewTable` WHERE Key_name = 'PRIMARY'");
        if ($keyStmt) {
            $keys = $keyStmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($keys) === 1) $primaryKey = $keys[0]['Column_name'];
        }

        $columnNames = array_column($tableSchema, 'Field');
        if ($sortBy === null && $primaryKey) $sortBy = $primaryKey;
        if ($sortBy && !in_array($sortBy, $columnNames)) {
            $sortBy = $primaryKey; // Fallback to PK if invalid column
        }
        
        $whereParts = [];
        $params = [];
        if ($search !== '') {
            $cols = array_column($tableSchema, 'Field');
            $conds = [];
            foreach ($cols as $col) {
                $conds[] = "`$col` LIKE ?";
                $params[] = "%$search%";
            }
            if ($conds) $whereParts[] = "(" . implode(' OR ', $conds) . ")";
        }

        if ($filterCol && in_array($filterCol, $columnNames)) {
            $whereParts[] = "`$filterCol` = ?";
            $params[] = $filterVal;
        }

        $where = !empty($whereParts) ? " WHERE " . implode(' AND ', $whereParts) : '';

        $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM `$viewTable` $where");
        $stmtCount->execute($params);
        $totalRows = $stmtCount->fetchColumn();

        $orderBy = '';
        if ($sortBy) $orderBy = "ORDER BY `$sortBy` $sortDir";

        $stmtData = $pdo->prepare("SELECT * FROM `$viewTable` $where $orderBy LIMIT $limit OFFSET $offset");
        $stmtData->execute($params);
        $tableData = $stmtData->fetchAll(PDO::FETCH_ASSOC);

        // Handle CSV download request
        if (isset($_GET['csv']) && $_GET['csv'] === '1') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $viewTable . '_' . date('Y-m-d') . '.csv"');
            $output = fopen('php://output', 'w');

            $orderByAll = $orderBy ?: ($primaryKey ? "ORDER BY `$primaryKey` ASC" : '');
            $stmtAll = $pdo->prepare("SELECT * FROM `$viewTable` $where $orderByAll");
            $stmtAll->execute($params);

            if ($row = $stmtAll->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, array_keys($row));
                do {
                    fputcsv($output, $row);
                } while ($row = $stmtAll->fetch(PDO::FETCH_ASSOC));
            }
            fclose($output);
            exit;
        }
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

function format_size($bytes) {
    if (!$bytes) return '0 B';
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
    return round($bytes / 1048576, 1) . ' MB';
}

admin_header('Database Manager');
?>
<div class="layout" style="grid-template-columns: 200px 1fr;">
    <aside class="nav" style="height: 100vh; position: sticky; top: 0;">
        <div class="brand">Tables</div>
        <div style="padding: 0 10px 10px 10px;">
            <input type="text" id="table-search-input" placeholder="Filter tables..." style="width:100%; padding:5px; box-sizing:border-box; border:1px solid #ccc; border-radius:4px;">
        </div>
        <div id="table-list" style="overflow-y: auto; height: calc(100vh - 100px);">
            <a href="database_diagram.php" style="display:block; padding: 5px 10px; color: #4caf50; font-weight:bold;">&#9737; Schema Diagram</a>
            <?php foreach ($tables as $table): 
                $sizeStr = isset($tableSizes[$table]) ? format_size($tableSizes[$table]) : '';
            ?>
                <a href="?table=<?php echo htmlspecialchars($table); ?>" class="table-link <?php echo $viewTable === $table ? 'active' : ''; ?>" style="display:flex; justify-content:space-between;">
                    <span><?php echo htmlspecialchars($table); ?></span> <small style="color:#888;"><?php echo $sizeStr; ?></small></a>
            <?php endforeach; ?>
        </div>
    </aside>
    <main class="main">
        <div class="card">
            <h2>Database Manager</h2>
            <p class="muted">View table schemas, browse data, and run raw SQL queries.</p>
            <?php if (isset($_GET['msg'])): ?><div class="card ok" style="margin-top:1rem;"><?php echo htmlspecialchars($_GET['msg']); ?></div><?php endif; ?>
            
            <?php if (!$viewTable): ?>
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #eee;">
                <strong>Maintenance:</strong> 
                <form method="post" style="display:inline; margin-left: 10px;" onsubmit="return confirm('Optimize all tables?');">
                    <input type="hidden" name="action" value="optimize">
                    <input type="hidden" name="table" value="ALL">
                    <button type="submit" style="background:#0288d1; padding: 5px 10px; font-size: 0.8rem;">Optimize All</button>
                </form>
                <form method="post" style="display:inline; margin-left: 5px;" onsubmit="return confirm('Repair all tables?');">
                    <input type="hidden" name="action" value="repair">
                    <input type="hidden" name="table" value="ALL">
                    <button type="submit" style="background:#f57c00; padding: 5px 10px; font-size: 0.8rem;">Repair All</button>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3>SQL Runner</h3>
            <div class="card err" style="margin-bottom: 1rem;"><b>Warning:</b> Running queries directly can corrupt your database. Use with extreme caution. No rollbacks are available.</div>
            <form method="post" onsubmit="return confirmDestructiveQuery();">
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
            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h3>Schema: `<?php echo htmlspecialchars($viewTable); ?>`</h3>
                    <div>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="action" value="optimize">
                            <input type="hidden" name="table" value="<?php echo htmlspecialchars($viewTable); ?>">
                            <button type="submit" style="background:#0288d1; padding: 5px 10px; font-size: 0.8rem;">Optimize</button>
                        </form>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="action" value="repair">
                            <input type="hidden" name="table" value="<?php echo htmlspecialchars($viewTable); ?>">
                            <button type="submit" style="background:#f57c00; padding: 5px 10px; font-size: 0.8rem;">Repair</button>
                        </form>
                        <form method="post" style="display:inline; margin-left: 5px;" onsubmit="return confirm('WARNING: This will delete ALL data in this table permanently. Continue?');">
                            <input type="hidden" name="action" value="truncate">
                            <input type="hidden" name="table" value="<?php echo htmlspecialchars($viewTable); ?>">
                            <button type="submit" style="background:#d32f2f; padding: 5px 10px; font-size: 0.8rem;">Truncate</button>
                        </form>
                    </div>
                </div>
                <?php echo render_table($tableSchema); ?>
            </div>
            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                    <h3>Data: `<?php echo htmlspecialchars($viewTable); ?>` (<?php echo $totalRows; ?> rows)</h3>
                    <div style="display:flex; align-items:center; gap:1rem;">
                        <a href="database_create.php?table=<?php echo urlencode($viewTable); ?>" class="button" style="background:#28a745;">Create New Row</a>
                        <a href="?table=<?php echo urlencode($viewTable); ?>&search=<?php echo urlencode($search); ?>&filter_col=<?php echo urlencode($filterCol); ?>&filter_val=<?php echo urlencode($filterVal); ?>&sort_by=<?php echo urlencode($sortBy); ?>&sort_dir=<?php echo urlencode($sortDir); ?>&csv=1" class="button" style="background:#00796b;">Download as CSV</a>
                        <form method="get" style="display:flex; gap:0.5rem;">
                            <input type="hidden" name="table" value="<?php echo htmlspecialchars($viewTable); ?>">
                            <input type="hidden" name="filter_col" value="<?php echo htmlspecialchars($filterCol); ?>">
                            <input type="hidden" name="filter_val" value="<?php echo htmlspecialchars($filterVal); ?>">
                            <input type="text" name="search" placeholder="Search table..." value="<?php echo htmlspecialchars($search); ?>" style="padding:0.4rem;">
                            <button type="submit" style="padding:0.4rem 0.8rem;">Search</button>
                            <?php if ($search): ?><a href="?table=<?php echo urlencode($viewTable); ?>" class="button" style="background:#777; text-decoration:none; display:inline-block; padding:0.4rem 0.8rem;">Clear</a><?php endif; ?>
                        </form>
                    </div>
                </div>
                <?php if ($filterCol): ?>
                    <div class="card info" style="margin-bottom: 1rem; background-color: #eef; padding: 0.75rem; border-radius: 6px;">
                        Filtering by <strong><?php echo htmlspecialchars($filterCol); ?></strong> = "<?php echo htmlspecialchars(mb_strimwidth($filterVal, 0, 50, '...')); ?>"
                        <a href="?table=<?php echo urlencode($viewTable); ?>&search=<?php echo urlencode($search); ?>" style="margin-left: 1rem; text-decoration: underline; color: #c62828;">Clear Filter</a>
                    </div>
                <?php endif; ?>
                <?php if (empty($tableData)): ?>
                    <p class="muted">No data to display.</p>
                <?php else: ?>
                <form method="post" id="bulk-action-form" onsubmit="return confirm('Are you sure you want to delete all selected rows? This action cannot be undone.');">
                    <input type="hidden" name="table" value="<?php echo htmlspecialchars($viewTable); ?>">
                    <input type="hidden" name="pk_name" value="<?php echo htmlspecialchars($primaryKey); ?>">
                    <table>
                        <thead><tr style="vertical-align: middle;">
                            <?php if ($primaryKey): ?><th style="width: 20px;"><input type="checkbox" id="select-all-checkbox" title="Select All"></th><?php endif; ?>
                            <?php foreach (array_column($tableSchema, 'Field') as $h): ?>
                                <?php
                                    $newSortDir = ($sortBy === $h && $sortDir === 'ASC') ? 'desc' : 'asc';
                                    $sortIndicator = '';
                                    if ($sortBy === $h) {
                                        $sortIndicator = ($sortDir === 'ASC') ? ' &uarr;' : ' &darr;';
                                    }
                                ?>
                                <th><a href="?table=<?php echo urlencode($viewTable); ?>&search=<?php echo urlencode($search); ?>&filter_col=<?php echo urlencode($filterCol); ?>&filter_val=<?php echo urlencode($filterVal); ?>&sort_by=<?php echo urlencode($h); ?>&sort_dir=<?php echo $newSortDir; ?>"><?php echo htmlspecialchars($h) . $sortIndicator; ?></a></th>
                            <?php endforeach; ?>
                            <?php if ($primaryKey): ?><th>Actions</th><?php endif; ?>
                        </tr></thead>
                        <tbody>
                        <?php foreach ($tableData as $row):
                            $pkVal = $row[$primaryKey] ?? null; ?>
                            <tr>
                                <?php if ($primaryKey): ?><td><input type="checkbox" name="selected_ids[]" value="<?php echo htmlspecialchars($pkVal); ?>" class="row-checkbox"></td><?php endif; ?>
                                <?php foreach ($row as $colName => $val): 
                                    $isEditable = $primaryKey && $colName !== $primaryKey;
                                    $fullVal = is_string($val) ? $val : (string)$val;
                                    $displayVal = mb_substr($fullVal, 0, 150);
                                    $filterQs = '&filter_col=' . urlencode($colName) . '&filter_val=' . urlencode($fullVal);
                                ?>
                                    <td class="<?php echo $isEditable ? 'editable-cell' : ''; ?>" data-table="<?php echo htmlspecialchars($viewTable); ?>" data-pk-name="<?php echo htmlspecialchars($primaryKey); ?>" data-pk-val="<?php echo htmlspecialchars($row[$primaryKey] ?? ''); ?>" data-col="<?php echo htmlspecialchars($colName); ?>" data-full-val="<?php echo htmlspecialchars($fullVal); ?>" title="<?php echo $isEditable ? 'Double click to edit' : 'Click to filter by this value'; ?>">
                                        <a href="?table=<?php echo urlencode($viewTable); ?>&search=<?php echo urlencode($search); ?><?php echo $filterQs; ?>" style="text-decoration:none; color:inherit;">
                                            <?php echo htmlspecialchars($displayVal) ?: '&nbsp;'; ?>
                                        </a>
                                    </td>
                                <?php endforeach; ?>
                                <?php if ($primaryKey): ?>
                                    <td style="white-space: nowrap;">
                                        <a href="database_edit.php?table=<?php echo urlencode($viewTable); ?>&pk_name=<?php echo urlencode($primaryKey); ?>&pk_value=<?php echo urlencode($row[$primaryKey]); ?>" class="button" style="padding: .3rem .6rem;">Edit</a>
                                        <form method="post" style="display:inline-block; margin-left:5px;" onsubmit="return confirm('Are you sure you want to delete this row?');">
                                            <input type="hidden" name="action" value="delete_row"><input type="hidden" name="table" value="<?php echo htmlspecialchars($viewTable); ?>"><input type="hidden" name="pk_name" value="<?php echo htmlspecialchars($primaryKey); ?>"><input type="hidden" name="pk_value" value="<?php echo htmlspecialchars($row[$primaryKey]); ?>">
                                            <button type="submit" style="background:#c62828; padding: .3rem .6rem;">Delete</button>
                                        </form>
                                        <form method="post" style="display:inline-block; margin-left:5px;" onsubmit="return confirm('Are you sure you want to duplicate this row?');">
                                            <input type="hidden" name="action" value="duplicate_row"><input type="hidden" name="table" value="<?php echo htmlspecialchars($viewTable); ?>"><input type="hidden" name="pk_name" value="<?php echo htmlspecialchars($primaryKey); ?>"><input type="hidden" name="pk_value" value="<?php echo htmlspecialchars($row[$primaryKey]); ?>">
                                            <button type="submit" style="background:#ff9800; padding: .3rem .6rem;" title="Duplicate">Duplicate</button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if ($primaryKey): ?>
                    <button type="submit" name="action" value="bulk_delete" style="margin-top: 1rem; background-color: #c62828;">Delete Selected</button>
                    <?php endif; ?>
                </form>
                <?php endif; ?>
                <div style="margin-top: 1rem;">
                    <?php $qs = '&search=' . urlencode($search) . '&filter_col=' . urlencode($filterCol) . '&filter_val=' . urlencode($filterVal) . '&sort_by=' . urlencode($sortBy) . '&sort_dir=' . strtolower($sortDir); ?>
                    <?php if ($page > 1): ?><a href="?table=<?php echo urlencode($viewTable); ?>&page=<?php echo $page - 1; ?><?php echo $qs; ?>" class="button">&laquo; Prev</a><?php endif; ?>
                    <?php if ($offset + $limit < $totalRows): ?><a href="?table=<?php echo urlencode($viewTable); ?>&page=<?php echo $page + 1; ?><?php echo $qs; ?>" class="button" style="margin-left: 10px;">Next &raquo;</a><?php endif; ?>
                </div>
            </div>
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
echo '
<script>
function confirmDestructiveQuery() {
    const query = document.querySelector(\'textarea[name="query"]\').value.trim().toUpperCase();
    const destructiveKeywords = ["DELETE", "DROP", "TRUNCATE", "ALTER", "UPDATE"];
    const isDestructive = destructiveKeywords.some(keyword => {
        const regex = new RegExp("\\\\b" + keyword + "\\\\b");
        return regex.test(query);
    });

    if (isDestructive) {
        return confirm("This query appears to be destructive. Are you sure you want to run it? There is no undo.");
    }
    return true;
}

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('table-search-input');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const links = document.querySelectorAll('#table-list a.table-link');
            links.forEach(link => {
                const text = link.textContent.toLowerCase();
                link.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }

    const selectAll = document.getElementById('select-all-checkbox');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    document.querySelectorAll('.editable-cell').forEach(cell => {
        cell.addEventListener('dblclick', function() {
            if (this.querySelector('input')) return; // Already editing
            
            const currentVal = this.getAttribute('data-full-val');
            const input = document.createElement('input');
            input.type = 'text';
            input.value = currentVal;
            input.style.width = '100%';
            input.style.boxSizing = 'border-box';
            
            const originalContent = this.innerHTML;
            this.innerHTML = '';
            this.appendChild(input);
            input.focus();
            
            const save = async () => {
                const newVal = input.value;
                const formData = new FormData();
                formData.append(\'action\', \'update_cell\');
                formData.append(\'table\', this.dataset.table);
                formData.append(\'pk_name\', this.dataset.pkName);
                formData.append(\'pk_value\', this.dataset.pkVal);
                formData.append(\'column\', this.dataset.col);
                formData.append(\'value\', newVal);
                
                try {
                    const res = await fetch(window.location.href, { method: \'POST\', body: formData });
                    const json = await res.json();
                    if (json.ok) {
                        this.setAttribute(\'data-full-val\', newVal);
                        this.textContent = newVal.length > 150 ? newVal.substring(0, 150) + \'...\' : newVal;
                        this.style.backgroundColor = \'#e8f5e9\'; // Flash green
                        setTimeout(() => this.style.backgroundColor = \'\', 1000);
                    } else {
                        alert(\'Update failed: \' + (json.error || \'Unknown error\'));
                        this.innerHTML = originalContent;
                    }
                } catch (e) {
                    alert(\'Network error\');
                    this.innerHTML = originalContent;
                }
            };

            input.addEventListener(\'blur\', save);
            input.addEventListener(\'keydown\', e => {
                if (e.key === \'Enter\') { e.preventDefault(); input.blur(); }
                if (e.key === \'Escape\') { this.innerHTML = originalContent; }
            });
        });
    });
});
</script></body></html>';
?>