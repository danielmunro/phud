<?php
namespace Phud\Affects;
use Phud\EasyInit,
	Phud\Actors\User,
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
	protected $level = 1;
	protected $args = [];
	protected $attributes = null;
	protected $listeners = [];
	
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
		$this->initListeners();
	}
	
	protected function initListeners() {}

	public function getAttribute($key)
	{
		return $this->attributes->getAttribute($key);
	}
	
	public function getAffect()
	{
		return $this->affect;
	}

	public function getLevel()
	{
		return $this->level;
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

	public function getListeners()
	{
		return $this->listeners;
	}

	public function decreaseTimeout()
	{
		$this->timeout--;
		return $this->timeout < 0;
	}
	
	public function apply($affectable)
	{
		if($affectable instanceof User && $this->getMessageStart()) {
			$affectable->getClient()->writeLine($this->getMessageStart());
		}
		$affectable->fire('affecting', $this);
		$affectable->addAffect($this);
		$this->applyTimeoutListener($affectable);
	}

	public function applyTimeoutListener($affectable)
	{
		foreach($this->listeners as $listener) {
			$affectable->on($listener[0], $listener[1]);
		}
		$affect = $this;
		$affectable->on(
			'tick',
			function($event, $affectable) use ($affect) {
				if($affect->decreaseTimeout()) {
					$affectable->removeAffect($affect);
					if($affectable instanceof User && $affect->getMessageEnd()) {
						$affectable->getClient()->writeLine($affect->getMessageEnd());
					}
					foreach($affect->getListeners() as $listener) {
						$affectable->unlisten($listener[0], $listener[1]);
					}
					$event->kill();
				}
			}
		);
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
		$this->initListeners();
	}
}
