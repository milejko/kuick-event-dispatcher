# Kuick Event Dispatcher
[![Latest Version](https://img.shields.io/github/release/milejko/kuick-event-dispatcher.svg?cacheSeconds=3600)](https://github.com/milejko/kuick-event-dispatcher/releases)
[![PHP](https://img.shields.io/badge/PHP-8.2%20|%208.3%20|%208.4%20|%208.5-blue?logo=php&cacheSeconds=3600)](https://www.php.net)
[![Total Downloads](https://img.shields.io/packagist/dt/kuick/event-dispatcher.svg?cacheSeconds=3600)](https://packagist.org/packages/kuick/event-dispatcher)
[![GitHub Actions CI](https://github.com/milejko/kuick-event-dispatcher/actions/workflows/ci.yml/badge.svg)](https://github.com/milejko/kuick-event-dispatcher/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/milejko/kuick-event-dispatcher/graph/badge.svg?token=M3FW3XYJ5J)](https://codecov.io/gh/milejko/kuick-event-dispatcher)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?cacheSeconds=14400)](LICENSE)

## PSR-14 Event Dispatcher lightweight implementation

### Key features
1. Full [PSR-14](https://www.php-fig.org/psr/psr-14/) compatibility
2. Easy to use listener registration
3. Listener prioritization
4. Support for wildcard listeners (ie. *, Prefix*)

### Examples
1. Registering listeners to the listener provider
```
<?php

use Kuick\Event\EventDispatcher;
use Kuick\Event\ListenerProvider;

$provider = new ListenerProvider();
$provider->registerListener(
    'some class name or pattern',
    function () {
        //handle the event
    }
);

$dispatcher = new EventDispatcher($provider);
// $dispatcher->dispatch(new SomeEvent());
```
2. Listener prioritization (using stdClass as an event)
```
<?php

use stdClass;
use Kuick\Event\EventDispatcher;
use Kuick\Event\ListenerPriority;
use Kuick\Event\ListenerProvider;

$provider = new ListenerProvider();
$provider->registerListener(
    stdClass::class,
    function (stdClass $event) {
        //handle the event
    },
    ListenerPriority::HIGH
);
$provider->registerListener(
    stdClass::class,
    function (stdClass $event) {
        //handle the event
    },
    ListenerPriority::LOW
);
$dispatcher = new EventDispatcher($provider);
// it should handle the event with high priority listener first
$dispatcher->dispatch(new stdClass());
```
3. Registering wildcard listeners (using stdClass as an event)
```
<?php

use stdClass;
use Kuick\Event\EventDispatcher;
use Kuick\Event\ListenerProvider;

$provider = new ListenerProvider();
$provider->registerListener(
    '*',
    function (object $event) {
        //handle the event
    }
);
$provider->registerListener(
    'std*',
    function (object $event) {
        //handle the event
    }
);
$dispatcher = new EventDispatcher($provider);
// it should match both listeners and run them sequentialy
$dispatcher->dispatch(new stdClass());
```