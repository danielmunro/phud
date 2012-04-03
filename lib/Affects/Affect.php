<?php
namespace Phud\Affects;
use Phud\Event\Subscriber,
	Phud\Event\Event,
	Phud\EasyInit,
	Phud\Debug,
	Phud\Server,
	Phud\Attributes;

class Affect
{
	use EasyInit;

	const GLOW = 'glow';
	const STUN = 'stun';
	
	protected $affect = '';
	protected $message_affect = '';
	protected $message_start = '';
	protected $message_end = '';
	protected $timeout = 0;
	protected $args = [];
	protected $attributes = null;
	protected $subscribers = [];
	
	public function __construct($properties = [])
	{
		$this->attributes = new Attributes();
		$this->initializeProperties($properties, [
			'attributes' => function($actor, $property, $value) {
				foreach($value as $attr => $attr_value) {
					$actor->setAttribute($attr, $attr_value);
				}
			},
			'apply' => function($affect, $property, $value) {
				$affect->apply($value);
			}
		]);
		$this->initSubscribers();
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

	public function getMessageStart()
	{
		return $this->message_start;
	}
	
	public function getMessageEnd()
	{
		return $this->message_end;
	}
	
	public function getTimeout()
	{
		return $this->timeout;
	}

	public function getSubscribers()
	{
		return $this->subscribers;
	}

	public function decreaseTimeout()
	{
		$this->timeout--;
		return $this->timeout < 0;
	}
	
	public function apply($affectable)
	{
		Debug::log("[Affect] Adding ".$this." to ".$affectable.", ".$this->timeout." tick timeout.");
		if($this->getMessageStart()) {
			Server::out($affectable, $this->getMessageStart());
		}
		$affectable->fire(Event::EVENT_APPLY_AFFECT, $this);
		$affectable->addAffect($this);
		$this->applyTimeoutSubscriber($affectable);
	}

	public function applyTimeoutSubscriber($affectable)
	{
		foreach($this->subscribers as $subscriber) {
			$affectable->addSubscriber($subscriber);
		}
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
						foreach($affect->getSubscribers() as $s) {
							$s->kill();
						}
						$subscriber->kill();
					}
				}
			)
		);
	}

	protected function initSubscribers()
	{
	}

	public function __toString()
	{
		return $this->affect;
	}

	public function __sleep()
	{
		return [
			'affect',
			'message_affect',
			'message_start',
			'message_end',
			'timeout',
			'args',
			'attributes'
		];
	}

	public function __wakeup()
	{
		$this->initSubscribers();
	}
}
?>
