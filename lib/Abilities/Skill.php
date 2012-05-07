<?php
namespace Phud\Abilities;
use Phud\Actors\Actor;

abstract class Skill extends Ability
{
	protected $event = '';
	protected $listener = null;

	public function __construct()
	{
		$skill = $this;
		switch($this->event) {
			case 'input':
				$this->listener = function($event, $actor, $args) use ($skill) {
					if(strpos($skill->getAlias(), $args[0]) === 0) {
						if($skill->perform($actor, $args)) {
							$actor->incrementDelay($skill->getDelay());
						}
						$event->satisfy();
					}
				};
				break;
			case 'attacked':
				$this->listener = function($event, $target) use ($skill) {
					if($skill->perform($target)) {
						$event->satisfy();
					}
				};
				break;
			case 'attack':
				$this->listener = function($event, $attacker) use ($skill) {
					$target = $attacker->getTarget();
					$event = $target->fire('attacked');
					if($event->getStatus() === 'on') {
						$skill->perform($attacker);
					}
				};
				break;
			default:
				$this->initializeListener();
				break;
		}
		parent::__construct();
	}

	protected function determineTarget(Actor $actor, $args)
	{
		return $actor->reconcileTarget($args);
	}

	public function getListener()
	{
		return [$this->event, $this->listener];
	}

	public function removeListener(Actor $actor)
	{
		$actor->unlisten($this->event, $this->listener);
	}
}
?>
