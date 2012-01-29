<?php
namespace Spells;
use \Mechanics\Ability\Spell,
	\Mechanics\Alias,
	\Mechanics\Actor,
	\Mechanics\Server;

class Cure_Light extends Spell
{
	protected $alias = 'cure light';
	protected $proficiency = 'healing';
	protected $required_proficiency = 20;
	protected $normal_modifier = ['wis'];

	protected function success(Actor $actor)
	{
		$prof_rand = rand(9, 11);
		$amount = round(rand(1, ($proficiency / $prof_rand) + 1));
		$target->modifyAttribute('hp', $amount);
		Server::out($target, "You feel better!");
	}
}
?>
