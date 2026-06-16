<?php

declare(strict_types=1);

namespace Componenta\Error\Renderer\Plates\Tests\Renderer;

use Componenta\Error\Context\CliContext;
use Componenta\Error\Renderer\PlatesRenderer;
use League\Plates\Engine;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[TestDox('PlatesRenderer')]
final class PlatesRendererTest extends TestCase
{
    public function testRenderUsesInjectedEngine(): void
    {
        $dir = $this->createTemplateDirectory([
            'error.php' => 'message=<?= $this->e($message) ?>;code=<?= $code ?>;debug=<?= $debug ? "yes" : "no" ?>',
        ]);

        try {
            $renderer = new PlatesRenderer(new Engine($dir));

            $output = $renderer->render(new RuntimeException('Plates renderer test', 500), CliContext::fromArgv());

            self::assertSame('message=Plates renderer test;code=500;debug=no', $output);
        } finally {
            $this->removeTemplateDirectory($dir);
        }
    }

    public function testRenderUsesStatusSpecificTemplateWhenItExists(): void
    {
        $dir = $this->createTemplateDirectory([
            'error.php' => 'default=<?= $code ?>',
            '404.php' => 'not-found=<?= $code ?>',
        ]);

        try {
            $renderer = new PlatesRenderer(new Engine($dir));

            $output = $renderer->render(new RuntimeException('Not Found', 404), CliContext::fromArgv());

            self::assertSame('not-found=404', $output);
        } finally {
            $this->removeTemplateDirectory($dir);
        }
    }

    /**
     * @param array<string, string> $templates
     */
    private function createTemplateDirectory(array $templates): string
    {
        $dir = sys_get_temp_dir() . '/componenta-plates-renderer-' . bin2hex(random_bytes(6));
        mkdir($dir);

        foreach ($templates as $name => $contents) {
            file_put_contents($dir . '/' . $name, $contents);
        }

        return $dir;
    }

    private function removeTemplateDirectory(string $dir): void
    {
        foreach (glob($dir . '/*.php') ?: [] as $file) {
            @unlink($file);
        }

        @rmdir($dir);
    }
}
