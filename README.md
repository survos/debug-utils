# survos/debug-utils

Assertion helpers that show the **acceptable** values on failure — not just "key not found".

When a CSV header, API payload, or config array has the wrong key, the mistake is almost always a typo, an underscore-vs-hyphen, or a capitalisation slip. A plain `array_key_exists` check tells you `name` is missing; these helpers tell you `name` is missing *and here are the keys that do exist* — so you spot `emial` next to `email` instantly.

Intentionally a **dev-only** dependency. Do not require it in production code.

## Requirements

- PHP 8.4+

## Installation

```bash
composer require --dev survos/debug-utils
```

## Usage

All helpers are static methods on `Survos\DebugUtils\Assert` and throw `\InvalidArgumentException` on failure.

### `keyExists()` — one key must be present

```php
use Survos\DebugUtils\Assert;

$row = ['name' => 'Tac', 'email' => 'tac@example.com'];

Assert::keyExists('email', $row);   // ok
Assert::keyExists('emial', $row);   // throws
```

```
Missing key [emial]:
email
name
```

Accepts arrays or objects, and string or integer keys. The available keys are sorted, so the output is stable across runs.

### `keysExist()` — several keys must all be present

```php
Assert::keysExist(['name', 'email', 'phone'], $row);
```

```
Missing keys [phone]:
email
name
```

### `inArray()` — value must be one of an allowed set

```php
Assert::inArray($status, ['draft', 'published', 'archived']);
```

```
Unexpected value 'pubished'. Allowed: 'archived', 'draft', 'published'
```

Comparison is **strict**, so `'1'` is not accepted where `1` is allowed.

### Custom messages

Every helper takes an optional trailing `$message` appended to the failure output:

```php
Assert::keyExists('id', $payload, 'check the /api/items response shape');
```

## Development

```bash
composer install
vendor/bin/phpunit          # run the test suite
vendor/bin/phpstan analyse  # static analysis
vendor/bin/ecs check        # coding standard
```

## License

MIT
