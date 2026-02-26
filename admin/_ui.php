<?php
require_once dirname(__DIR__) . '/bootstrap.php';
date_default_timezone_set($config['timezone'] ?? 'UTC');

function admin_header(string $title): void
{ 
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo htmlspecialchars($title); ?> - Admin</title>
        <style>
            :root {
                --bg:#f3f6fb;
                --panel:#ffffff;
                --border:#dfe6f1;
                --text:#10213d;
                --muted:#5f7193;
                --accent:#1d7fff;
            }
            body { margin:0; font-family:"Segoe UI",Tahoma,sans-serif; background:var(--bg); color:var(--text); }
            .layout { display:grid; grid-template-columns:240px 1fr; min-height:100vh; }
            .nav { background:#0f2344; color:#fff; padding:1rem; }
            .brand { font-weight:700; margin-bottom:1rem; }
            .nav a { display:block; color:#d6e3ff; text-decoration:none; padding:.55rem .6rem; border-radius:8px; margin-bottom:.35rem; }
            .nav a:hover, .nav a.active { background:rgba(255,255,255,.12); color:#fff; }
            .main { padding:1.2rem; }
            .card { background:var(--panel); border:1px solid var(--border); border-radius:12px; padding:1rem; margin-bottom:1rem; }
            .grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
            .kpi { font-size:1.5rem; font-weight:700; }
            .muted { color:var(--muted); }
            table { width:100%; border-collapse: collapse; }
            th,td { text-align:left; border-bottom:1px solid var(--border); padding:.55rem .4rem; font-size:.9rem; }
            input,select,textarea { width:100%; padding:.6rem; border:1px solid var(--border); border-radius:8px; }
            button { border:0; background:var(--accent); color:#fff; padding:.65rem .9rem; border-radius:8px; cursor:pointer; }
            .row { display:grid; grid-template-columns:1fr 1fr; gap:.8rem; }
            .ok { color:#0f7a31; font-weight:600; }
            .err { color:#a21f29; font-weight:600; }
            .barchart { display: flex; flex-direction: column; gap: 0.5rem; }
            .barchart-item { display: grid; grid-template-columns: 150px 1fr 50px; align-items: center; gap: 1rem; font-size: 0.9rem; }
            .barchart-label { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .barchart-bar-container { background: #eee; border-radius: 4px; height: 1rem; }
            .barchart-bar { background: var(--accent); height: 100%; border-radius: 4px; }
            .barchart-value { text-align: right; font-weight: 600; color: var(--muted); }
            @media (max-width: 980px) {
                .layout { grid-template-columns:1fr; }
                .grid { grid-template-columns:1fr; }
                .row { grid-template-columns:1fr; }
            }
        </style>
    </head>
    <body>
    <div class="layout">
        <aside class="nav">
            <div class="brand">AI Admin</div>
            <a href="dashboard.php">Dashboard</a>
            <a href="training.php">Training</a>
            <a href="knowledge.php">Knowledge</a>
            <a href="files.php">File Manager</a>
            <a href="database.php">Database</a>
            <a href="analytics.php">Analytics</a>
            <a href="settings.php">Settings</a>
            <a href="../index.php">Open Chat</a>
        </aside>
        <main class="main">
    <?php
}

function admin_footer(): void
{
    echo '</main></div></body></html>';
}
