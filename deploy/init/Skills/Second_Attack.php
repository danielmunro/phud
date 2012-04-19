<?php
namespace Phud\Abilities;
use Phud\Actors\Actor;

class Second_Attack extends Skill
{
	protected $alias = 'second attack';
	protected $proficiency = 'melee';
	protected $required_proficiency = 30;
	protected $normal_modifier = ['dex', 'str'];
	protected $event = 'attack';

	protected function initializeListener()
	{
		$this->listener = function($fighter) {
			$ability->perform($fighter);
		};
	}

	protected function success(Actor $actor)
	{
		$event = $actor->getTarget()->fire('attacked');
		if($event->getStatus() === 'on') {
			$actor->attack('2nd');
		}
	}
}

?>
