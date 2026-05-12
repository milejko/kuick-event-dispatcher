<?php

namespace Kuick\Event\Tests\Unit;

use Kuick\EventDispatcher\ListenerPriority;
use Kuick\EventDispatcher\ListenerProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\Kuick\EventDispatcher\Mocks\MockEvent;

#[CoversClass(ListenerProvider::class)]
#[CoversClass(ListenerPriority::class)]
class ListenerProviderTest extends TestCase
{
    public function testIfAddedListenerCanBeRetrieved(): void
    {
        $provider = new ListenerProvider();
        $listener = function () {
        };
        $provider->registerListener(MockEvent::class, $listener);
        $this->assertEquals([$listener], $provider->getListenersForEvent(new MockEvent()));
    }

    public function testIfListenerPriorityWorks(): void
    {
        $provider = new ListenerProvider();
        $listener1 = function (): int {
            return 1;
        };
        $listener2 = function (): int {
            return 2;
        };
        $listener3 = function (): int {
            return 3;
        };
        $listener4 = function (): int {
            return 4;
        };
        $provider->registerListener(MockEvent::class, $listener1, ListenerPriority::LOW);
        $provider->registerListener(MockEvent::class, $listener2, ListenerPriority::NORMAL);
        $provider->registerListener(MockEvent::class, $listener3, ListenerPriority::HIGH);
        $provider->registerListener(MockEvent::class, $listener4, ListenerPriority::LOWEST);
        $providers = $provider->getListenersForEvent(new MockEvent());
        $this->assertCount(4, $providers);
        $this->assertEquals(3, $providers[0]());
        $this->assertEquals(2, $providers[1]());
        $this->assertEquals(1, $providers[2]());
        $this->assertEquals(4, $providers[3]());
        $this->assertEmpty($provider->getListenersForEvent(new stdClass()));
    }

    public function testIfWildcardRegistrationWorks(): void
    {
        $provider = new ListenerProvider();
        $listener1 = function (): void {
        };
        $listener2 = function (): int {
            return 1;
        };
        $listener3 = function (): string {
            return 'test';
        };
        $listener4 = function (): object {
            return new stdClass();
        };
        $provider->registerListener('WillNotMatchAThing*', $listener1);
        $provider->registerListener(MockEvent::class, $listener2);
        $provider->registerListener('*', $listener3);
        $provider->registerListener('std*', $listener4);
        $this->assertEquals([$listener2, $listener3], $provider->getListenersForEvent(new MockEvent()));
        $this->assertEquals([$listener3, $listener4], $provider->getListenersForEvent(new stdClass()));
    }
}
