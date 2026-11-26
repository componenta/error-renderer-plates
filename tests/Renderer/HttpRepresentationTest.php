<?php

declare(strict_types=1);

namespace Componenta\Error\Renderer\Plates\Tests\Renderer;

use Componenta\Error\ErrorOccurrence;
use Componenta\Error\Context\HttpContext;
use Componenta\Error\Renderer\PlatesRenderer;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class HttpRepresentationTest extends TestCase
{
    public function testHttpRepresentationDeclaresActualHtml(): void
    {
        $renderer = new PlatesRenderer(new \League\Plates\Engine(dirname(__DIR__, 2) . '/templates'));
        $context = new HttpContext(new ServerRequest('GET', '/', ['Accept' => 'application/json']));
        $result = $renderer->render(new ErrorOccurrence(new RuntimeException('rendering test'), 'ERR-test'), $context);
        self::assertSame('text/html; charset=utf-8', $result->contentType);
        self::assertNotEmpty($result->content);
        self::assertSame($result->content, (string) $result);
    }

    public function testBundledProductionTemplatesDoNotExposeExceptionDetailsOrClaimReporting(): void
    {
        $renderer = new PlatesRenderer(new \League\Plates\Engine(dirname(__DIR__, 2) . '/templates'));
        $context = new HttpContext(new ServerRequest('GET', '/'));
        foreach ([404, 500, 503] as $status) {
            $error = new ErrorOccurrence(new RuntimeException('private-diagnostic-message'), 'ERR-bundled');
            $html = $renderer->render($error, $context->withAttribute(\Componenta\Error\Context\ErrorContextAttribute::HTTP_STATUS_CODE, $status))->content;
            self::assertStringContainsString('ERR-bundled', $html);
            self::assertStringNotContainsString('private-diagnostic-message', $html);
            self::assertStringNotContainsString('fonts.googleapis.com', $html);
            self::assertStringNotContainsString('The error has been', $html);
        }
    }
}
