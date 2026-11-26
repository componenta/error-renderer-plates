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
$error = (new \Componenta\Error\ErrorOccurrenceFactory())->create($exception);
$result = $renderer->render($error, $context);
$html = (string) $result;
$contentType = $result->contentType;
```

`PlatesRenderer` requires a `League\Plates\Engine`. The constructor also accepts:

- `$defaultTemplate`, used when no specific template matches;
- `$debug`, which controls whether file, line, trace, and previous exception data are passed to templates;
- `$templateMap`, where keys may be exception class names or HTTP status codes.

Bundled templates are stored in `templates/` and include generic error, 404, and 500 pages.

## Boundary

This package only provides a renderer. It does not provide the application template helper from `componenta/templater-app` and does not handle response emission.

`render()` returns the core `RenderedError` value, with `content`, `contentType`, and explicit string conversion. Requires Error Handler 2.

Templates receive the original `ErrorOccurrence` as `$error`; use `$error->id` for correlation. Bundled templates omit exception messages in non-debug mode and do not claim reporting succeeded. Custom templates are trusted PHP and still receive the original exception and context; the debug flag is not a sandbox.
