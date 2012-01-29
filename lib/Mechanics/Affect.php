<?php
namespace Mechanics;
use \Living\User,
	\Mechanics\Event\Subscriber,
	\Mechanics\Event\Event;

class Affect
{

	const GLOW = 'glow';
	const STUN = 'stun';
	
	private $affect = '';
	private $message_affect = '';
	private $message_end = '';
	private $timeout = 0;
	private $args = array();
	private $attributes = null;
	
	public function __construct($properties = [])
	{
		$this->attributes = new Attributes();
		foreach($properties as $property => $value) {
			if(property_exists($this, $property)) {
				if($property === 'attributes') {
					foreach($value as $a => $v) {
						$this->attributes->setAttribute($a, $v);
					}
				} else {
					$this->$property = $value;
				}
			} else if($property === 'apply') {
				$this->apply($value);
			}
		}
	}
	
	public function getAttributes()
	{
		return $this->attributes;
	}

	public function getAttribute($key)
	{
		return $this->attributes->getAttribute($key);
	}
	
	public function setAffect($affect)
	{
		$this->affect = $affect;
	}
	
	public function getAffect()
	{
		return $this->affect;
	}
	
	public function setMessageAffect($message)
	{
		$this->message_affect = $message;
	}
	
	public function getMessageAffect()
	{
		return $this->message_affect;
	}
	
	public function setMessageEnd($message)
	{
		$this->message_end = $message;
	}
	
	public function getMessageEnd()
	{
		return $this->message_end;
	}
	
	public function setTimeout($timeout)
	{
		$this->timeout = $timeout;
	}
	
	public function getTimeout()
	{
		return $this->timeout;
	}

	public function decreaseTimeout()
	{
		$this->timeout--;
		return $this->timeout < 0;
	}
	
	public function apply($affectable)
	{
		Debug::addDebugLine("[Affect] Adding ".$this." to ".$affectable.", ".$this->timeout." tick timeout.");
		$affectable->fire(Event::EVENT_APPLY_AFFECT, $this);
		$affectable->addAffect($this);
		$this->applyTimeoutSubscriber($affectable);
	}

	public function applyTimeoutSubscriber($affectable)
	{
		if($this->timeout > 0) {
			Server::instance()->addSubscriber(
				new Subscriber(
					Event::EVENT_TICK,
					$this,
					function($subscriber, $server, $affect) use ($affectable) {
						if($affect->decreaseTimeout()) {
							$affectable->removeAffect($affect);
							if($affect->getMessageEnd() && $affectable instanceof User) {
								Server::out($affectable, $affect->getMessageEnd());
							}
							$subscriber->kill();
						}
					}
				)
			);
		}
	}

	public function __toString()
	{
		return $this->affect;
	}
}
?>
