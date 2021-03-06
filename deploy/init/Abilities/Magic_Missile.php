<?php
namespace Phud\Abilities;
use Phud\Actors\Actor;

class Magic_Missile extends Spell
{
	protected $alias = 'magic missile';
	protected $is_offensive = true;
	protected $proficiency = 'sorcery';
	protected $required_proficiency = 20;
	protected $normal_modifier = ['int'];
	
	protected function success(Actor $actor, Actor $target)
	{
		$proficiency = $actor->getProficiencyScore($this->proficiency);
		$damage = -(round(rand($proficiency / 10, $proficiency / 5))); 
		$actor->notify("Your magic missile hits ".$target.'!');
		$target->modifyAttribute('hp', $damage);
	}
}
