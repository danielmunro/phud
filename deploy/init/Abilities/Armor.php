<?php
namespace Phud\Abilities;
use Phud\Affect,
	Phud\Actors\Actor,
	Phud\Server;

class Armor extends Spell
{
	protected $alias = 'armor';
	protected $proficiency = 'benedictions';
	protected $required_proficiency = 20;
	protected $normal_modifier = ['wis'];

	protected function success(Actor $actor, Actor $target)
	{
		$proficiency = $actor->getProficiencyScore($this->proficiency);
		$timeout = min(30, ceil($proficiency / 2));
		$mod_ac = min(-(round($proficiency / 2)), -15);
		
		$a = new Affect([
			'affect' => 'armor',
			'message_affect' => 'Spell: armor: '.$mod_ac.' to armor class',
			'attributes' => [
				'ac_slash' => $mod_ac,
				'ac_bash' => $mod_ac,
				'ac_pierce' => $mod_ac,
				'ac_magic' => $mod_ac
			],
			'apply' => $target
		]);

		Server::out($target, "You feel more protected!");
	}
}
?>
