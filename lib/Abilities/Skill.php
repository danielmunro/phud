<?php
namespace Phud\Abilities;
use Phud\Actors\Actor;

abstract class Skill extends Ability
{
	protected $event = '';
	protected $listener = null;

	public function __construct()
	{
		$this->initializeListener();
		parent::__construct();
	}

	protected function getInputListener()
	{
		if(empty($this->input_listener)) {
			$skill = $this;
			$this->input_listener = function($actor, $args) use ($skill) {
				if(strpos($skill->getAlias(), $args[0]) === 0) {
					if($skill->perform($actor, $args)) {
						$actor->incrementDelay($skill->getDelay());
					}
					return 'satisfy';
				}
			};
		}
		return $this->input_listener;
	}

	protected function determineTarget(Actor $actor, $args)
	{
		return $actor->reconcileTarget($args);
	}

	abstract protected function initializeListener();

	public function applyListener(Actor $actor)
	{
		$actor->on($this->event, $this->listener);
	}

	public function removeListener(Actor $actor)
	{
		$actor->unlisten($this->event, $this->listener);
	}
}
?>
