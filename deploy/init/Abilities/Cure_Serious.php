<?php
namespace Phud\Abilities;
use Phud\Actors\Actor;

class Cure_Serious extends Spell
{
	protected $alias = 'cure serious';
	protected $proficiency = 'healing';
	protected $required_proficiency = 35;
	protected $normal_modifier = ['wis'];

	protected function success(Actor $actor)
	{
		$prof_rand = rand(9, 11);
		$amount = round(rand(5, ($proficiency / $prof_rand) + 4));
		$target->modifyAttribute('hp', $amount);
		$target->notify("You feel better!");
	}
}
