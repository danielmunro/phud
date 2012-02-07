<?php
namespace Mechanics\Event;
use \Mechanics\Debug;

trait Broadcaster
{
	protected $_subscribers = [];
	protected $_subscribers_deferred = [];

	public function addSubscriber(Subscriber $subscriber)
	{
		$t = $subscriber->getEventType();
		if($subscriber->isDeferred()) {
			$this->_subscribers_deferred[$t][] = $subscriber;
		} else {
			$this->_subscribers[$t][] = $subscriber;
		}
	}

	public function removeSubscriber(Subscriber $subscriber)
	{
		$t = $subscriber->getEventType();
		if(!empty($this->_subscribers[$t])) {
			$key = array_search($subscriber, $this->_subscribers[$t]);
			if(is_numeric($key)) {
				unset($this->_subscribers[$t][$key]);
			}
		}
		if(!empty($this->_subscribers_deferred[$t])) {
			$key = array_search($subscriber, $this->_subscribers_deferred[$t]);
			if(is_numeric($key)) {
				unset($this->_subscribers_deferred[$t][$key]);
			}
		}
	}

	public function fire($event_type, &$a1 = null, &$a2 = null, &$a3 = null, &$a4 = null)
	{
		if(!isset($this->_subscribers[$event_type])) {
			$this->_subscribers[$event_type] = [];
		}
		if(!isset($this->_subscribers_deferred[$event_type])) {
			$this->_subscribers_deferred[$event_type] = [];
		}
		$is_satisfied = $this->_fire($this->_subscribers[$event_type], $a1, $a2, $a3, $a4);
		if(!$is_satisfied) {
			$is_satisfied = $this->_fire($this->_subscribers_deferred[$event_type], $a1, $a2, $a3, $a4);
		}
		return $is_satisfied;
	}

	private function _fire($subscribers, &$a1, &$a2, &$a3, &$a4)
	{
		foreach($subscribers as $i => $subscriber) {
			$callback = $subscriber->getCallback();

			// Hacky but it's the only way to pass the parameters by reference. Calling
			// call_user_func_array() will pass all parameters by value, breaking all modifiers
			if($subscriber->getSubscriber()) {
				$callback($subscriber, $this, $subscriber->getSubscriber(), $a1, $a2, $a3, $a4);
			} else {
				$callback($subscriber, $this, $a1, $a2, $a3, $a4);
			}
			if($subscriber->isKilled()) {
				$subscriber->satisfyBroadcast();
				unset($this->_subscribers[$subscriber->getEventType()][$i]);
			}
			$is_satisfied = $subscriber->isBroadcastSatisfied();
			if($is_satisfied) {
				$subscriber->satisfyBroadcast(false);
				return $is_satisfied;
			}
		}
	}
}
?>
