<?php
/**
 * 404 Not Found error template
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

$this->layout('layout', ['title' => '404 - Not Found']);

$path = '/404';
if (isset($context) && method_exists($context, 'request')) {
    $path = $context->request->getUri()->getPath();
} elseif (isset($context) && property_exists($context, 'request')) {
    $path = $context->request->getUri()->getPath();
}
?>

<pre class="ascii-code">    d8888   .d8888b.      d8888  
   d8P888  d88P  Y88b    d8P888  
  d8P 888  888    888   d8P 888  
 d8P  888  888    888  d8P  888  
d88   888  888    888 d88   888  
8888888888 888    888 8888888888 
      888  Y88b  d88P       888  
      888   "Y8888P"        888  </pre>

<pre class="ascii-text"> _   _  ___ _____   _____ ___  _   _ _   _ ____  
| \ | |/ _ \_   _| |  ___/ _ \| | | | \ | |  _ \ 
|  \| | | | || |   | |_ | | | | | | |  \| | | | |
| |\  | |_| || |   |  _|| |_| | |_| | |\  | |_| |
|_| \_|\___/ |_|   |_|   \___/ \___/|_| \_|____/ </pre>

<p>There is no <span class="error-code"><?= $this->e($path) ?></span> page on this server!</p>

<p>The path you requested doesn't exist in <span class="white">Componenta</span> application.</p>
<p>Check your <span class="white">routes</span> configuration or return to <span class="green">home</span>.</p>

<p>Type <span class="green">help</span> to get list of available commands.</p>

<?php if (!empty($debug) && !empty($file)): ?>
<div class="debug-section">
    <details>
        <summary>Debug information</summary>
        <p><span class="yellow">Exception:</span> <?= $this->e($type ?? 'Unknown') ?></p>
        <p><span class="yellow">Message:</span> <?= $this->e($message ?? 'Page not found') ?></p>
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
