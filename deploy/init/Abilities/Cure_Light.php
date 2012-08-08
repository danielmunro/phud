<?php
namespace Phud\Abilities;
use Phud\Actors\Actor;

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
		$target->notify("You feel better!");
	}
}
