# Componenta Error Renderer Plates

Интеграция рендерера League Plates для `componenta/error-handler`. Пакет рендерит ошибки через шаблоны Plates и реализует `ErrorRendererInterface`.

## Граница пакета

Пакет предоставляет только `PlatesRenderer`. Он не предоставляет вспомогательную функцию шаблонов из `componenta/templater-app`, не выбирает политику отчётов об ошибках и не отправляет HTTP-ответ.

## Установка

```bash
composer require componenta/error-renderer-plates
```

## Основной API

```php
use Componenta\Error\Renderer\PlatesRenderer;
use League\Plates\Engine;

$engine = new Engine(__DIR__ . '/templates');
$renderer = new PlatesRenderer($engine);
$html = $renderer->render($exception, $context);
```

`PlatesRenderer` требует `League\Plates\Engine`. Конструктор также принимает:

- `$defaultTemplate`, который используется, когда нет более точного шаблона;
- `$debug`, который включает передачу файла, строки, стека вызовов и предыдущего исключения в шаблон;
- `$templateMap`, где ключами могут быть имена классов исключений или HTTP-статусы.

Встроенные шаблоны находятся в `templates/` и включают общую страницу ошибки, 404 и 500.
