<?php
namespace Spells;
use \Mechanics\Ability\Spell,
	\Mechanics\Actor,
	\Mechanics\Server;

class Magic_Missile extends Spell
{
	protected $alias = 'magic missile';
	protected $is_offensive = true;
	protected $proficiency = 'sorcery';
	protected $required_proficiency = 20;
	protected $normal_modifier = ['int'];
	
	protected function success(Actor $actor, Actor $target)
	{
		$proficiency = $actor->getProficiencyIn($this->proficiency);
		$damage = -(round(rand($proficiency / 10, $proficiency / 5))); 
		$target->modifyAttribute('hp', $damage);
		Server::out($actor, "Your magic missile hits ".$target.'!');
	}
}
?>
