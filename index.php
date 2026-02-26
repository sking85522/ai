<?php
require_once __DIR__ . '/bootstrap.php';

date_default_timezone_set($config['timezone'] ?? 'UTC');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$_SESSION['chat_history'] = $_SESSION['chat_history'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $wantsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')
        || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

    $input = trim((string) ($_POST['input_text'] ?? ''));
    $uploadToken = '';
    if (isset($_FILES['image_file']) && is_array($_FILES['image_file']) && (int) ($_FILES['image_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $tmp = (string) ($_FILES['image_file']['tmp_name'] ?? '');
        $name = (string) ($_FILES['image_file']['name'] ?? 'image');
        $ext = strtolower((string) pathinfo($name, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) && is_uploaded_file($tmp)) {
            $dir = __DIR__ . '/storage/uploads';
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }
            $dest = $dir . '/' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            if (@move_uploaded_file($tmp, $dest)) {
                $uploadToken = '__image_file__:' . $dest;
            }
        }
    }

    if ($input !== '' || $uploadToken !== '') {
        try {
            $effectiveInput = $uploadToken !== '' ? $uploadToken : $input;
            $engine = new Engine();
            $result = $engine->run($effectiveInput);

            $conversation = new Conversation();
            $conversation->log(
                ($uploadToken !== '' ? '[image-upload]' : $input),
                $result['response'],
                $result['analysis']['intent'],
                (float) $result['analysis']['confidence'],
                $result['analysis']['input_language'] ?? 'unknown',
                $result['analysis']['response_language'] ?? 'unknown',
                $result['context'] ?? [],
                $result['analysis']['tokens'] ?? []
            );

            $_SESSION['chat_history'][] = ['role' => 'user', 'text' => ($uploadToken !== '' ? '[Image uploaded]' : $input), 'time' => date('H:i')];
            $_SESSION['chat_history'][] = ['role' => 'assistant', 'text' => $result['response'], 'time' => date('H:i')];
            $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -200);

            if ($wantsJson) {
                if (ob_get_level() > 0) {
                    @ob_clean();
                }
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'ok' => true,
                    'reply' => $result['response'],
                    'analysis' => $result['analysis'],
                    'history' => $_SESSION['chat_history'],
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        } catch (Throwable $e) {
            error_log('JyotiAI POST error: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
            if ($wantsJson) {
                if (ob_get_level() > 0) {
                    @ob_clean();
                }
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'ok' => false,
                    'reply' => 'Server issue aayi thi, maine retry-safe mode activate kiya hai. Please ek baar phir message bhejo.',
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
    } elseif ($wantsJson) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'reply' => 'Empty input.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (isset($_GET['new_chat']) && $_GET['new_chat'] === '1') {
    $_SESSION['chat_history'] = [];
    header('Location: index.php');
    exit;
}

$history = $_SESSION['chat_history'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($config['app_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #343541;
            --sidebar-bg: #202123;
            --input-bg: #40414f;
            --text-primary: #ececf1;
            --text-secondary: #c5c5d2;
            --user-msg-bg: #343541;
            --ai-msg-bg: #444654;
            --border-color: #4d4d4f;
            --accent-color: #10a37f;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            height: 100vh;
            display: flex;
            overflow: hidden;
        }
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            padding: 0.5rem;
            flex-shrink: 0;
        }
        .new-chat-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.2s;
            margin-bottom: 1rem;
        }
        .new-chat-btn:hover { background-color: #2b2c2f; }
        .nav-links { flex: 1; overflow-y: auto; }
        .history-item {
            display: block;
            padding: 10px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            border-radius: 6px;
            margin-bottom: 2px;
        }
        .history-item:hover { background-color: #2a2b32; color: var(--text-primary); }
        .admin-link {
            margin-top: auto;
            padding: 12px;
            color: var(--text-secondary);
            text-decoration: none;
            border-top: 1px solid var(--border-color);
        }
        .admin-link:hover { color: var(--text-primary); }
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            height: 100%;
        }
        .chat-window {
            flex: 1;
            overflow-y: auto;
            scroll-behavior: smooth;
            padding-bottom: 120px;
        }
        .message-row {
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding: 24px 0;
            width: 100%;
        }
        .message-row.assistant { background-color: var(--ai-msg-bg); }
        .message-row.user { background-color: var(--user-msg-bg); }
        .message-content {
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            gap: 20px;
            padding: 0 20px;
        }
        .avatar {
            width: 30px;
            height: 30px;
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
        }
        .avatar.user { background-color: #5436da; color: white; }
        .avatar.assistant { background-color: var(--accent-color); color: white; }
        .message-text {
            flex: 1;
            line-height: 1.6;
            font-size: 1rem;
            white-space: pre-wrap;
        }
        .meta-time { font-size: 0.7rem; color: #8e8ea0; margin-top: 5px; }
        .input-container {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: linear-gradient(180deg, rgba(53,53,65,0), var(--bg-color) 20%);
            padding: 2rem 1rem;
        }
        .input-box-wrapper {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            background-color: var(--input-bg);
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border: 1px solid rgba(32,33,35,0.5);
            display: flex;
            align-items: flex-end;
            padding: 10px;
        }
        textarea {
            width: 100%;
            background: transparent;
            border: none;
            color: white;
            font-family: inherit;
            font-size: 1rem;
            resize: none;
            max-height: 200px;
            padding: 4px 10px;
            outline: none;
            line-height: 1.5;
        }
        .send-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 4px;
            transition: color 0.2s, background 0.2s;
        }
        .send-btn:hover { background-color: #202123; color: var(--text-primary); }
        .send-btn svg { width: 16px; height: 16px; fill: currentColor; }
        .icon-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 4px;
            transition: color 0.2s, background 0.2s;
        }
        .icon-btn:hover { background-color: #202123; color: var(--text-primary); }
        .icon-btn svg { width: 16px; height: 16px; fill: currentColor; }
        .icon-btn.active { color: var(--accent-color); }
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .message-content { padding: 0 15px; }
        }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #565869; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #acacbe; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <a href="?new_chat=1" class="new-chat-btn">+ New Chat</a>
        <div class="nav-links">
            <?php if (!$history): ?>
                <div style="padding: 0 10px; font-size: 0.75rem; color: #8e8ea0; margin-bottom: 5px;">Recent</div>
            <?php else: ?>
                <?php foreach (array_reverse(array_slice($history, -10)) as $h): ?>
                    <?php if ($h['role'] === 'user'): ?>
                    <a href="#" class="history-item">
                        <?php echo htmlspecialchars(mb_substr($h['text'], 0, 25)) . '...'; ?>
                    </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <a href="admin/dashboard.php" class="admin-link">Admin Panel</a>
    </aside>

    <main class="main-content">
        <div class="chat-window" id="chatWindow">
            <?php if (!$history): ?>
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 80%; text-align: center; opacity: 0.8;">
                    <h2 style="font-size: 2rem; font-weight: 600; margin-bottom: 1rem;"><?php echo htmlspecialchars($config['app_name']); ?></h2>
                    <p>Ask anything in Hindi or English. Version: <?php echo htmlspecialchars((string) ($config['app_version'] ?? '0.0.1')); ?></p>
                </div>
            <?php endif; ?>

            <?php foreach ($history as $msg): ?>
                <div class="message-row <?php echo $msg['role']; ?>">
                    <div class="message-content">
                        <div class="avatar <?php echo $msg['role']; ?>">
                            <?php echo $msg['role'] === 'user' ? 'U' : 'AI'; ?>
                        </div>
                        <div class="message-text">
                            <?php echo nl2br(htmlspecialchars($msg['text'])); ?>
                            <div class="meta-time"><?php echo htmlspecialchars($msg['time']); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="input-container">
            <form id="chatForm" method="post" action="index.php" enctype="multipart/form-data">
                <div class="input-box-wrapper">
                    <textarea id="inputText" name="input_text" rows="1" placeholder="Send a message..."></textarea>
                    <input type="file" id="imageInput" name="image_file" accept="image/png,image/jpeg,image/webp" style="display:none;">
                    <button type="button" class="icon-btn" id="imageBtn" title="Upload image">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2zM8.5 11A2.5 2.5 0 1 1 11 8.5 2.5 2.5 0 0 1 8.5 11zM5 19l4.5-6 3.5 4.5 2.5-3L19 19z"/></svg>
                    </button>
                    <button type="button" class="icon-btn" id="micBtn" title="Voice input">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 14a3 3 0 0 0 3-3V5a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3zm5-3a1 1 0 1 1 2 0 7 7 0 0 1-6 6.92V21h3a1 1 0 1 1 0 2H8a1 1 0 1 1 0-2h3v-3.08A7 7 0 0 1 5 11a1 1 0 1 1 2 0 5 5 0 0 0 10 0z"/></svg>
                    </button>
                    <button type="button" class="icon-btn" id="speakBtn" title="Speak last answer">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M14 3.23v17.54a1 1 0 0 1-1.64.77L7.6 17H4a2 2 0 0 1-2-2v-6a2 2 0 0 1 2-2h3.6l4.76-4.54A1 1 0 0 1 14 3.23zM17.54 8.46a1 1 0 0 1 1.41 0 5 5 0 0 1 0 7.08 1 1 0 0 1-1.41-1.41 3 3 0 0 0 0-4.26 1 1 0 0 1 0-1.41zm2.83-2.83a1 1 0 0 1 1.41 0 9 9 0 0 1 0 12.74 1 1 0 1 1-1.41-1.41 7 7 0 0 0 0-9.92 1 1 0 0 1 0-1.41z"/></svg>
                    </button>
                    <button type="submit" class="send-btn">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    </button>
                </div>
            </form>
            <div style="text-align: center; font-size: 0.75rem; color: #8e8ea0; margin-top: 10px;">
                AI can make mistakes. Consider checking important information.
            </div>
        </div>
    </main>

<script>
(() => {
    const form = document.getElementById('chatForm');
    const input = document.getElementById('inputText');
    const chatWindow = document.getElementById('chatWindow');
    const imageInput = document.getElementById('imageInput');
    const imageBtn = document.getElementById('imageBtn');
    const micBtn = document.getElementById('micBtn');
    const speakBtn = document.getElementById('speakBtn');
    let lastAssistantReply = '';
    let recognitionActive = false;

    // Auto-resize textarea
    input.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
        if(this.value === '') this.style.height = 'auto';
        if (window.speechSynthesis && window.speechSynthesis.speaking) {
            window.speechSynthesis.cancel();
        }
    });

    // Enter to send
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
    });

    const createMessageRow = (role, text) => {
        const row = document.createElement('div');
        row.className = 'message-row ' + role;
        
        const content = document.createElement('div');
        content.className = 'message-content';
        
        const avatar = document.createElement('div');
        avatar.className = 'avatar ' + role;
        avatar.textContent = role === 'user' ? 'U' : 'AI';
        
        const msgText = document.createElement('div');
        msgText.className = 'message-text';
        msgText.innerHTML = nl2br(escapeHtml(text)); // Use innerHTML for <br>
        
        const meta = document.createElement('div');
        meta.className = 'meta-time';
        meta.textContent = new Date().toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
        
        msgText.appendChild(meta);
        content.appendChild(avatar);
        content.appendChild(msgText);
        row.appendChild(content);
        
        return row;
    };

    const scrollToBottom = () => {
        chatWindow.scrollTop = chatWindow.scrollHeight;
    };
    
    const escapeHtml = (unsafe) => {
        return unsafe
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }

    const nl2br = (str) => {
        return str.replace(/(?:\r\n|\r|\n)/g, '<br>');
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = input.value.trim();
        const hasImage = imageInput && imageInput.files && imageInput.files.length > 0;
        if (!text && !hasImage) return;

        // Clear empty message if it exists
        const emptyDiv = chatWindow.querySelector('div[style*="display: flex"]');
        if (emptyDiv) {
            emptyDiv.remove();
        }

        chatWindow.appendChild(createMessageRow('user', text || '[Image uploaded]'));
        scrollToBottom();
        
        input.value = '';
        input.style.height = 'auto';

        try {
            const fd = new FormData();
            fd.append('input_text', text);
            if (hasImage) {
                fd.append('image_file', imageInput.files[0]);
            }
            const res = await fetch('index.php', {
                method: 'POST',
                body: fd,
                headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}
            });
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            const ctype = res.headers.get('content-type') || '';
            let data;
            if (ctype.includes('application/json')) {
                data = await res.json();
            } else {
                const raw = await res.text();
                data = {reply: raw ? raw.slice(0, 300) : 'Server returned non-JSON response.'};
            }
            lastAssistantReply = data.reply || 'No response';
            chatWindow.appendChild(createMessageRow('assistant', lastAssistantReply));
            scrollToBottom();
            if (imageInput) imageInput.value = '';
        } catch (err) {
            console.error(err);
            chatWindow.appendChild(createMessageRow('assistant', 'Server connection error. Retry once. If it repeats, check PHP error log.'));
            scrollToBottom();
        }
    });

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (SpeechRecognition && micBtn) {
        const recognition = new SpeechRecognition();
        recognition.lang = 'hi-IN';
        recognition.interimResults = true;
        recognition.continuous = true;
        recognition.maxAlternatives = 1;

        micBtn.addEventListener('click', () => {
            if (recognitionActive) {
                recognition.stop();
                recognitionActive = false;
                micBtn.classList.remove('active');
                return;
            }
            const hasHindi = /[\u0900-\u097F]/.test(input.value);
            recognition.lang = hasHindi ? 'hi-IN' : 'en-US';
            micBtn.classList.add('active');
            recognitionActive = true;
            recognition.start();
        });

        recognition.addEventListener('result', (event) => {
            const last = event.results[event.results.length - 1];
            const text = ((last && last[0] && last[0].transcript) || '').trim();
            if (text) {
                input.value = text;
                input.dispatchEvent(new Event('input'));
                input.focus();
            }
        });

        recognition.addEventListener('end', () => {
            recognitionActive = false;
            micBtn.classList.remove('active');
        });
        recognition.addEventListener('error', () => {
            recognitionActive = false;
            micBtn.classList.remove('active');
        });
    } else if (micBtn) {
        micBtn.style.display = 'none';
    }

    if (window.speechSynthesis && speakBtn) {
        speakBtn.addEventListener('click', () => {
            if (!lastAssistantReply) {
                return;
            }
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(lastAssistantReply);
            utterance.lang = /[\u0900-\u097F]/.test(lastAssistantReply) ? 'hi-IN' : 'en-US';
            window.speechSynthesis.speak(utterance);
        });
    } else if (speakBtn) {
        speakBtn.style.display = 'none';
    }

    if (imageBtn && imageInput) {
        imageBtn.addEventListener('click', () => imageInput.click());
        imageInput.addEventListener('change', () => {
            if (imageInput.files && imageInput.files.length > 0) {
                input.value = input.value.trim() || '[Image uploaded]';
                input.dispatchEvent(new Event('input'));
            }
        });
    }

    // Handle PHP-rendered messages line breaks on initial load
    document.querySelectorAll('.message-text').forEach(el => {
        // Check if it doesn't already contain HTML elements (like the meta-time div)
        const hasChildren = el.children.length > 0;
        if(hasChildren) {
            // This logic assumes the text is the first node
            const textNode = Array.from(el.childNodes).find(node => node.nodeType === Node.TEXT_NODE);
            if (textNode) {
                 const html = nl2br(textNode.textContent);
                 textNode.remove();
                 el.insertAdjacentHTML('afterbegin', html);
            }
        }
    });


    scrollToBottom();
})();
</script>
</body>
</html>
