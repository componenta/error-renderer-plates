# Componenta Error Renderer Plates

League Plates renderer integration for `componenta/error-handler`. The package renders errors through bundled Plates templates.

## Installation

```bash
composer require componenta/error-renderer-plates
```

## Main API

```php
use Componenta\Error\Renderer\PlatesRenderer;
use League\Plates\Engine;

$engine = new Engine(__DIR__ . '/templates');
$renderer = new PlatesRenderer($engine);
$html = $renderer->render($exception, $context);
```

`PlatesRenderer` requires a `League\Plates\Engine`. The constructor also accepts:

- `$defaultTemplate`, used when no specific template matches;
- `$debug`, which controls whether file, line, trace, and previous exception data are passed to templates;
- `$templateMap`, where keys may be exception class names or HTTP status codes.

Bundled templates are stored in `templates/` and include generic error, 404, and 500 pages.

## Boundary

This package only provides a renderer. It does not provide the application template helper from `componenta/templater-app` and does not handle response emission.
