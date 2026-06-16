<?php
/**
 * Generic error template
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

$code = $code ?? 500;
$this->layout('layout', ['title' => $code . ' - Error']);

$errorMessages = [
    400 => 'BAD REQUEST',
    401 => 'UNAUTHORIZED', 
    403 => 'FORBIDDEN',
    404 => 'NOT FOUND',
    405 => 'METHOD NOT ALLOWED',
    408 => 'REQUEST TIMEOUT',
    429 => 'TOO MANY REQUESTS',
    500 => 'SERVER ERROR',
    502 => 'BAD GATEWAY',
    503 => 'SERVICE UNAVAILABLE',
    504 => 'GATEWAY TIMEOUT',
];

$errorText = $errorMessages[$code] ?? 'ERROR';
?>

<pre class="ascii-code"><?= $this->e(str_pad((string)$code, 3, '0', STR_PAD_LEFT)) ?></pre>

<pre class="ascii-text"><?= $this->e($errorText) ?></pre>

<p>Error <span class="error-code"><?= $code ?></span>: <?= $this->e($message ?? $errorText) ?></p>

<p>Something went wrong in <span class="white">Componenta</span> application.</p>
<p>The error has been <span class="white">logged</span> and we'll look into it.</p>

<p>Type <span class="green">help</span> to get list of available commands.</p>

<?php if (!empty($debug) && !empty($file)): ?>
<div class="debug-section">
    <details open>
        <summary>Debug information</summary>
        <p><span class="yellow">Exception:</span> <?= $this->e($type ?? 'Unknown') ?></p>
        <p><span class="yellow">Message:</span> <?= $this->e($message ?? 'Unknown error') ?></p>
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
