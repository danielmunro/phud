<?php
namespace Mechanics\Event;
use \Mechanics\Debug,
	\Closure;

class Subscriber
{
	const DEFERRED = true;

	protected $event_type = '';
	protected $subscriber = null;
	protected $callback = null;
	protected $killed = false;
	protected $suppressed = false;
	protected $broadcast_satisfied = false;
	protected $deferred = false;

	public function __construct($event_type, $subscriber, $callback = null, $deferred = false)
	{
		// method overloading would be nice
		$this->event_type = $event_type;
		if($callback instanceof Closure) {
			$this->subscriber = $subscriber;
			$this->callback = $callback;
			$this->deferred = $deferred;
		} else {
			$this->subscriber = null;
			$this->callback = $subscriber;
			$this->deferred = $callback;
		}
	}

	public function getEventType()
	{
		return $this->event_type;
	}

	public function getSubscriber()
	{
		return $this->subscriber;
	}

	public function getCallback()
	{
		return $this->callback;
	}

	public function isDeferred()
	{
		return $this->deferred;
	}

	public function kill()
	{
		$this->killed = true;
	}

	public function isKilled()
	{
		return $this->killed;
	}

	public function satisfyBroadcast($satisfied = true)
	{
		$this->broadcast_satisfied = $satisfied;
	}

	public function isBroadcastSatisfied()
	{
		return $this->broadcast_satisfied;
	}

	public function suppress($suppress = true)
	{
		$this->suppressed = $suppress;
	}

	public function isSuppressed()
	{
		return $this->suppressed;
	}
	
	public function __toString()
	{
		return $this->subscriber." is observing for [".$this->event_type."]";
	}
}
?>
