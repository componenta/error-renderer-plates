<?php
/**
 * 500 Server Error template
 * 
 * @var Throwable $exception
 * @var \Componenta\Error\ErrorContextInterface $context
 * @var bool $debug
 * @var int $code
 * @var string $message
 * @var string $type
 * @var string|null $file (debug only)
 * @var int|null $line (debug only)
 * @var array|null $trace (debug only)
 */

$this->layout('layout', ['title' => '500 - Server Error']);
?>

<pre class="ascii-code">888888888  .d8888b.   .d8888b.  
888       d88P  Y88b d88P  Y88b 
888       888    888 888    888 
8888888b. 888    888 888    888 
     "Y88b888    888 888    888 
       888888    888 888    888 
Y88b  d88PY88b  d88P Y88b  d88P 
 "Y8888P"  "Y8888P"   "Y8888P"  </pre>

<pre class="ascii-text"> ____  _____ ______     _______ ____  
/ ___|| ____|  _ \ \   / / ____|  _ \ 
\___ \|  _| | |_) \ \ / /|  _| | |_) |
 ___) | |___|  _ < \ V / | |___|  _ < 
|____/|_____|_| \_\ \_/  |_____|_| \_\

 _____ ____  ____   ___  ____         
| ____|  _ \|  _ \ / _ \|  _ \        
|  _| | |_) | |_) | | | | |_) |       
| |___|  _ <|  _ <| |_| |  _ <        
|_____|_| \_\_| \_\\___/|_| \_\       </pre>

<p>Oops! <span class="error-code">500</span> Internal Server Error</p>

<p>Something went wrong in <span class="white">Componenta</span> application.</p>
<p>The error has been <span class="white">logged</span> and we'll look into it.</p>

<p>Type <span class="green">help</span> to get list of available commands.</p>

<?php if (!empty($debug) && !empty($file)): ?>
<div class="debug-section">
    <details open>
        <summary>Debug information</summary>
        <p><span class="yellow">Exception:</span> <?= $this->e($type ?? 'Unknown') ?></p>
        <p><span class="yellow">Message:</span> <?= $this->e($message ?? 'Internal server error') ?></p>
        <p><span class="yellow">File:</span> <span class="trace-file"><?= $this->e($file) ?></span>:<span class="trace-line-num"><?= $line ?></span></p>
        <?php if (!empty($trace)): ?>
        <p><span class="yellow">Stack trace:</span></p>
        <pre class="trace"><?php foreach ($trace as $i => $frame): ?>
<span class="trace-line">#<?= $i ?> <?php if (isset($frame['file'])): ?><span class="trace-file"><?= $this->e($frame['file']) ?></span>:<span class="trace-line-num"><?= $frame['line'] ?? '?' ?></span> <?php endif ?><?= isset($frame['class']) ? $this->e($frame['class'] . $frame['type']) : '' ?><?= $this->e($frame['function'] ?? '') ?>()</span>
<?php endforeach ?></pre>
        <?php endif ?>
    </details>
</div>
<?php endif ?>
