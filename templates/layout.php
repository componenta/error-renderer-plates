<?php
/**
 * Base error layout template
 * 
 * @var string $title Page title
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->e($title ?? 'Error') ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Fira Code', 'Consolas', 'Monaco', monospace;
            background: #0d0d0d;
            color: #909090;
            min-height: 100vh;
            padding: 50px;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .terminal {
            max-width: 700px;
        }
        
        pre {
            font-family: inherit;
        }
        
        .ascii-code {
            color: #cccccc;
            white-space: pre;
            font-size: 13px;
            line-height: 1.2;
            margin-bottom: 8px;
        }
        
        .ascii-text {
            color: #666666;
            white-space: pre;
            font-size: 13px;
            line-height: 1.2;
            margin-bottom: 40px;
        }
        
        p {
            margin: 14px 0;
        }
        
        .white {
            color: #e0e0e0;
        }
        
        .error-code {
            color: #cc4444;
        }
        
        .green {
            color: #44aa44;
        }
        
        .yellow {
            color: #aa8844;
        }
        
        a {
            color: #5588dd;
            text-decoration: underline;
            text-underline-offset: 3px;
        }
        
        a:hover {
            color: #77aaff;
        }
        
        .prompt-line {
            margin-top: 35px;
            display: flex;
            align-items: center;
        }
        
        .prompt {
            color: #44aa44;
        }
        
        .cursor {
            display: inline-block;
            width: 9px;
            height: 17px;
            background: #909090;
            animation: blink 1s step-end infinite;
            margin-left: 2px;
        }
        
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }
        
        .input-area {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        
        .typed-text {
            color: #909090;
        }
        
        .debug-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #333;
        }
        
        .debug-section summary {
            cursor: pointer;
            color: #666;
            margin-bottom: 10px;
        }
        
        .debug-section summary:hover {
            color: #909090;
        }
        
        .trace {
            font-size: 12px;
            color: #666;
            overflow-x: auto;
            padding: 15px;
            background: #111;
            border-radius: 4px;
            max-height: 300px;
            overflow-y: auto;
        }
        
        .trace-line {
            margin: 4px 0;
        }
        
        .trace-file {
            color: #5588dd;
        }
        
        .trace-line-num {
            color: #aa8844;
        }
    </style>
</head>
<body>
    <div class="terminal">
        <?= $this->section('content') ?>
        
        <div class="prompt-line">
            <span class="prompt">&gt;</span>
            <span class="cursor"></span>
            <input type="text" class="input-area" autofocus>
        </div>
    </div>
    
    <script>
        const input = document.querySelector('.input-area');
        const terminal = document.querySelector('.terminal');
        const promptLine = document.querySelector('.prompt-line');
        
        const commands = {
            help: () => `Available commands:
  help     - show this message
  home     - go to homepage
  back     - go to previous page
  retry    - retry the request
  clear    - clear terminal`,
            home: () => { window.location.href = '/'; },
            back: () => { history.back(); },
            retry: () => { location.reload(); },
            clear: () => { 
                document.querySelectorAll('.output').forEach(el => el.remove());
                return null;
            }
        };
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                const cmd = input.value.trim().toLowerCase();
                input.value = '';
                updateCursor();
                
                if (cmd && commands[cmd]) {
                    const result = commands[cmd]();
                    if (result) addOutput(result);
                } else if (cmd) {
                    addOutput(`Command not found: ${cmd}\nType 'help' for available commands.`);
                }
            }
        });
        
        input.addEventListener('input', updateCursor);
        
        function updateCursor() {
            const cursor = document.querySelector('.cursor');
            const text = input.value;
            
            const oldText = document.querySelector('.typed-text');
            if (oldText) oldText.remove();
            
            if (text) {
                const textSpan = document.createElement('span');
                textSpan.className = 'typed-text';
                textSpan.textContent = text;
                promptLine.insertBefore(textSpan, cursor);
            }
        }
        
        function addOutput(text) {
            const p = document.createElement('pre');
            p.className = 'output';
            p.style.color = '#909090';
            p.style.margin = '14px 0';
            p.textContent = text;
            terminal.insertBefore(p, promptLine);
        }
        
        document.body.addEventListener('click', () => input.focus());
        input.focus();
    </script>
</body>
</html>
