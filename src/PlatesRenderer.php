<?php

declare(strict_types=1);

namespace Componenta\Error\Renderer;

use Componenta\Error\ErrorContextInterface;
use Componenta\Error\ErrorOccurrence;
use League\Plates\Engine;
use Throwable;

/**
 * Template-based error renderer using League Plates.
 */
readonly class PlatesRenderer implements ErrorRendererInterface
{
    /** @var array<class-string<Throwable>|int, string> */
    private array $templateMap;

    /**
     * @param string $defaultTemplate Default template name
     * @param bool $debug Pass debug info to templates
     * @param array<class-string<Throwable>|int, string> $templateMap Exception type or HTTP code to template mapping
     */
    public function __construct(
        private Engine $engine,
        private string $defaultTemplate = 'error',
        private bool $debug = false,
        array $templateMap = [],
    ) {
        $snapshot = [];
        foreach ($templateMap as $key => $template) {
            if ((!is_int($key) && !is_a($key, Throwable::class, true))
                || (is_int($key) && ($key < 400 || $key > 599))
                || !is_string($template) || $template === '') {
                throw new \InvalidArgumentException('Invalid error template mapping.');
            }
            $snapshot[$key] = $template;
        }
        $this->templateMap = $snapshot;
    }

    public function render(ErrorOccurrence $error, ErrorContextInterface $context): RenderedError
    {
        $exception = $error->exception;
        $status = $this->statusCode($exception, $context);
        return new RenderedError(
            $this->engine->render(
                $this->resolveTemplate($exception, $status),
                $this->buildPayload($error, $context, $status),
            ),
            'text/html; charset=utf-8',
        );
    }

    public function supports(ErrorOccurrence $error, ErrorContextInterface $context): bool
    {
        return true;
    }

    /**
     * @return array{
     *     error: ErrorOccurrence,
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
    private function buildPayload(ErrorOccurrence $error, ErrorContextInterface $context, int $status): array
    {
        $exception = $error->exception;
        $payload = [
            'error' => $error,
            'exception' => $exception,
            'context' => $context,
            'debug' => $this->debug,
            'code' => $status,
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

    private function resolveTemplate(Throwable $exception, int $code): string
    {
        foreach ($this->templateMap as $key => $template) {
            if (is_string($key) && $exception instanceof $key) {
                return $template;
            }
        }

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
