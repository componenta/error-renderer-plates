<?php

declare(strict_types=1);

namespace Componenta\Error\Renderer;

use Componenta\Error\ErrorContextInterface;
use Componenta\Error\Renderer\ErrorRendererInterface;
use League\Plates\Engine;
use Throwable;

/**
 * Template-based error renderer using League Plates.
 */
readonly class PlatesRenderer implements ErrorRendererInterface
{
    /**
     * @param string $defaultTemplate Default template name
     * @param bool $debug Pass debug info to templates
     * @param array<class-string<Throwable>|int, string> $templateMap Exception type or HTTP code to template mapping
     */
    public function __construct(
        private Engine $engine,
        private string $defaultTemplate = 'error',
        private bool $debug = false,
        private array $templateMap = [],
    ) {
    }

    public function render(Throwable $exception, ErrorContextInterface $context): string
    {
        return $this->engine->render(
            $this->resolveTemplate($exception),
            $this->buildPayload($exception, $context),
        );
    }

    public function supports(Throwable $exception, ErrorContextInterface $context): bool
    {
        return true;
    }

    /**
     * @return array{
     *     exception: Throwable,
     *     context: ErrorContextInterface,
     *     debug: bool,
     *     code: int,
     *     message: string,
     *     type: class-string<Throwable>,
     *     file?: string,
     *     line?: int,
     *     trace?: array<int, array<string, mixed>>,
     *     previous?: Throwable|null
     * }
     */
    private function buildPayload(Throwable $exception, ErrorContextInterface $context): array
    {
        $payload = [
            'exception' => $exception,
            'context' => $context,
            'debug' => $this->debug,
            'code' => $this->statusCode($exception),
            'message' => $exception->getMessage(),
            'type' => $exception::class,
        ];

        if ($this->debug) {
            $payload['file'] = $exception->getFile();
            $payload['line'] = $exception->getLine();
            $payload['trace'] = $exception->getTrace();
            $payload['previous'] = $exception->getPrevious();
        }

        return $payload;
    }

    private function resolveTemplate(Throwable $exception): string
    {
        foreach ($this->templateMap as $key => $template) {
            if (is_string($key) && $exception instanceof $key) {
                return $template;
            }
        }

        $code = $this->statusCode($exception);

        if (isset($this->templateMap[$code])) {
            return $this->templateMap[$code];
        }

        if ($this->engine->exists((string) $code)) {
            return (string) $code;
        }

        return $this->defaultTemplate;
    }

    private function statusCode(Throwable $exception): int
    {
        $code = $exception->getCode();

        return $code >= 400 && $code < 600 ? $code : 500;
    }
}
