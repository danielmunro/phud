<?php
namespace Mechanics;
use \Living\User,
	\Mechanics\Event\Subscriber,
	\Mechanics\Event\Event;

class Affect
{
	use EasyInit;

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
		$this->initializeProperties($properties, [
			'attributes' => function($actor, $property, $value) {
				foreach($value as $attr => $attr_value) {
					$actor->getAttributes()->setAttribute($attr, $attr_value);
				}
			},
			'apply' => function($affect, $property, $value) {
				$affect->apply($value);
			}
		]);
	}
	
	public function getAttributes()
	{
		return $this->attributes;
	}

	public function getAttribute($key)
	{
		return $this->attributes->getAttribute($key);
	}
	
	public function getAffect()
	{
		return $this->affect;
	}
	
	public function getMessageAffect()
	{
		return $this->message_affect;
	}
	
	public function getMessageEnd()
	{
		return $this->message_end;
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
		Debug::log("[Affect] Adding ".$this." to ".$affectable.", ".$this->timeout." tick timeout.");
		$affectable->fire(Event::EVENT_APPLY_AFFECT, $this);
		$affectable->addAffect($this);
		$this->applyTimeoutSubscriber($affectable);
	}

	public function applyTimeoutSubscriber($affectable)
	{
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

	public function __toString()
	{
		return $this->affect;
	}
}
?>
