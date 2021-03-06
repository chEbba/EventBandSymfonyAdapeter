<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Adapter\Symfony;

use EventBand\AbstractSubscription;
use EventBand\BandDispatcher;
use EventBand\Event;
use Symfony\Component\EventDispatcher\Event as SymfonyEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class SymfonySubscription
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class ListenerSubscription extends AbstractSubscription
{
    private $listener;
    private $eventDispatcher;

    public function __construct($eventName, callable $listener, EventDispatcherInterface $eventDispatcher, $band = null)
    {
        $this->listener = $listener;
        $this->eventDispatcher = $eventDispatcher;

        parent::__construct($eventName, $band);
    }

    public function getListener()
    {
        return $this->listener;
    }

    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(Event $event, BandDispatcher $dispatcher)
    {
        $symfonyEvent = $event instanceof SymfonyEvent ? $event : new SymfonyEventWrapper($event);
        $symfonyEvent->setDispatcher($this->getEventDispatcher());

        call_user_func($this->listener, $symfonyEvent, $this->getEventName(), $this->getEventDispatcher());

        return !$symfonyEvent->isPropagationStopped();
    }
}