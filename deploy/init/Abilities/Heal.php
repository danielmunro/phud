<?php
namespace Phud\Abilities;
use Phud\Actors\Actor;

class Heal extends Spell
{
	protected $alias = 'heal';
	protected $proficiency = 'healing';
	protected $required_proficiency = 55;
	protected $normal_modifier = ['wis'];

	protected function success(Actor $actor)
	{
		$prof_rand = rand(9, 11);
		$amount = round(rand(45, ($proficiency / $prof_rand) + 45));
		$actor->modifyAttribute('hp', $amount);
		$actor->notify("You feel better!");
	}
}
