<?php

declare(strict_types=1);

namespace Componenta\Error\Renderer\Plates\Tests\Renderer;

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
        $result = $renderer->renderHttp(new RuntimeException('rendering test'), $context);
        self::assertSame('text/html; charset=utf-8', $result->contentType);
        self::assertNotEmpty($result->body);
    }
}
