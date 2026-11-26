<?php

declare(strict_types=1);

namespace Componenta\Error\Renderer;

use Componenta\Error\ErrorContextInterface;
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

    public function render(Throwable $exception, ErrorContextInterface $context): RenderedError
    {
        return new RenderedError(
            $this->engine->render(
                $this->resolveTemplate($exception, $context),
                $this->buildPayload($exception, $context),
            ),
            'text/html; charset=utf-8',
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
            'code' => $this->statusCode($exception, $context),
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

    private function resolveTemplate(Throwable $exception, ErrorContextInterface $context): string
    {
        foreach ($this->templateMap as $key => $template) {
            if (is_string($key) && $exception instanceof $key) {
                return $template;
            }
        }

        $code = $this->statusCode($exception, $context);

        if (isset($this->templateMap[$code])) {
            return $this->templateMap[$code];
        }

        if ($this->engine->exists((string) $code)) {
            return (string) $code;
        }

        return $this->defaultTemplate;
    }

    private function statusCode(Throwable $exception, ErrorContextInterface $context): int
    {
        if ($context instanceof \Componenta\Error\Context\HttpErrorContextInterface) {
            $status = $context->getAttribute(\Componenta\Error\Context\ErrorContextAttribute::HTTP_STATUS_CODE, null);
            if (is_int($status) && $status >= 400 && $status < 600) {
                return $status;
            }
            return (new \Componenta\Error\Http\HttpStatusResolver())->resolve($exception, $context);
        }
        $code = $exception->getCode();
        return is_int($code) && $code >= 400 && $code < 600 ? $code : 500;
    }
}
