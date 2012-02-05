<?php
namespace Spells;
use \Mechanics\Ability\Spell,
	\Mechanics\Alias,
	\Mechanics\Actor,
	\Mechanics\Affect,
	\Mechanics\Server;

class Shield extends Spell
{
	protected $alias = 'shield';
	protected $proficiency = 'benedictions';
	protected $required_proficiency = 25;
	protected $saving_attribute = 'wis';

	protected function success(Actor $actor, Actor $target)
	{
		$timeout = min(30, ceil($proficiency / 2));
		$mod_ac = min(-(round($proficiency / 1.5)), -30);
		
		$a = new Affect([
			'affect' => 'shield',
			'message_affect' => 'Spell: shield: '.$mod_ac.' to armor class',
			'message_end' => 'You feel less protected.',
			'timeout' => $timeout,
			'apply' => $target,
			'attributes' => [
				'ac_bash' => $mod_ac,
				'ac_slash' => $mod_ac,
				'ac_pierce' => $mod_ac,
				'ac_magic' => $mod_ac
			]
		]);
		Server::out($target, "You feel more protected!");
	}
}
?>
