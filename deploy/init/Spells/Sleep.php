<?php
namespace Spells;
use Phud\Ability\Spell,
	Phud\Actor\Actor,
	Phud\Affect,
	Phud\Server;

class Sleep extends Spell
{
	protected $alias = 'sleep';
	protected $proficiency = 'beguiling';
	protected $required_proficiency = 40;
	protected $normal_modifier = ['int'];
	protected $easy_modifier = ['cha'];

	protected function success(Actor $actor, Actor $target)
	{
		$proficiency = $actor->getProficiencyIn($this->proficiency);
		$timeout = round(1 + ($proficiency / 10));
		$target->setDisposition(Actor::DISPOSITION_SLEEPING);
		$a = new Affect([
			'affect' => 'sleep',
			'message_affect' => 'Spell: sleep',
			'timeout' => $timeout,
			'apply' => $target
		]);
		$target->getRoom()->announce2([
			['actor' => $target, 'message' => 'You go to sleep.'],
			['actor' => '*', 'message'  => ucfirst($target).' goes to sleep.']
		]);
	}
}
?>
