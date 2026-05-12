# Kuick Event Dispatcher — Copilot Instructions

## Commands

```bash
# Install dependencies
composer install

# Run all checks (phpcs → phpstan → phpmd → phpunit)
composer test:all

# Individual checks
composer test:phpcs        # PSR-12 coding standard
composer test:phpstan      # Static analysis (level 9)
composer test:phpmd        # Mess detection
composer test:phpunit      # Unit tests with coverage

# Auto-fix coding standard issues
composer fix:phpcbf

# Run a single test class
XDEBUG_MODE=coverage vendor/bin/phpunit tests/Unit/EventDispatcherTest.php

# Run a single test method
XDEBUG_MODE=coverage vendor/bin/phpunit --filter testIfStoppableEventsAreHandledCorrectly

# Full CI run (Docker-based, matches GitHub Actions)
make test
```

## Architecture

Three source files in `src/`, namespace `Kuick\EventDispatcher`:

- **`ListenerPriority`** — constants only (`LOWEST` … `HIGHEST`). Higher integer = higher priority. Default is `NORMAL = 0`.
- **`ListenerProvider`** — stores listeners as `array{pattern, listener, priority}` tuples. `getListenersForEvent()` does wildcard pattern matching against `get_class($event)`, then `krsort`s by priority key before returning a flat `callable[]`.
- **`EventDispatcher`** — iterates listeners returned by the provider; checks `StoppableEventInterface::isPropagationStopped()` before each call.

`EventDispatcher` accepts any `ListenerProviderInterface`, but the concrete `ListenerProvider` is the only implementation in this package.

## Key Conventions

### Listener registration
`registerListener(string $eventNameOrPattern, callable $listener, int $priority)` returns `self` for fluent chaining. The pattern supports `*` wildcards (e.g., `'*'` matches everything, `'Kuick\Event\*'` matches any event under that prefix).

### Priority ordering
Listeners with the same priority are dispatched in **registration order**. Across priorities, higher integer wins. Use `ListenerPriority` constants rather than raw integers.

### PSR-14 compliance
`EventDispatcher::dispatch()` always returns the event object. Propagation stops only when the event implements `StoppableEventInterface` and `isPropagationStopped()` returns `true`.

### Testing conventions
- Test classes live under `tests/Unit/`, namespace `Kuick\Event\Tests\Unit` (note: namespace differs from autoload config `Tests\Kuick\EventDispatcher` — this inconsistency exists in the repo).
- Mock event classes live in `tests/Mocks/`, namespace `Tests\Kuick\EventDispatcher\Mocks`.
- Every test class must have a `#[CoversClass(ClassName::class)]` attribute (`requireCoverageMetadata` is active in phpunit.xml). Import `PHPUnit\Framework\Attributes\CoversClass`.
- `phpunit.xml` has `failOnRisky="true"` and `failOnWarning="true"` — avoid risky tests (tests with no assertions, output, etc.).

### Code style
PSR-12 enforced via phpcs/phpcbf. PHPStan runs at **level 9** — all types must be fully specified. All source files use the BSD-style file-level docblock with `@link`, `@copyright`, and `@license` tags.
