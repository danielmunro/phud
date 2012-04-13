<?php
namespace Phud\Abilities;
use Phud\Event,
	Phud\Actors\Actor;

class Shield_Block extends Skill
{
	protected $alias = 'shield block';
	protected $proficiency = 'melee';
	protected $required_proficiency = 25;
	protected $normal_modifier = ['dex'];
	protected $hard_modifier = ['str'];
	protected $event = 'attacked';
	
	protected function applyCost(Actor $actor)
	{
		if($actor->getAttribute('movement') >= 2) {
			$actor->modifyAttribute('movement', -2);
			return true;
		}
		return false;
	}

	protected function success(Actor $actor, Actor $target, $args)
	{
		$sexes = [Actor::SEX_MALE => 'his', Actor::SEX_FEMALE => 'her', Actor::SEX_NEUTRAL => 'its'];
		$s = $actor->getDisplaySex($sexes);
		$actor->getRoom()->announce2([
			['actor' => $actor, 'message' => 'You block '.$target."'s attack with your shield!"],
			['actor' => $target, 'message' => ucfirst($actor).' blocks your attack with '.$s.' shield!'],
			['actor' => '*', 'message' => ucfirst($actor).' blocks '.$target."'s attack with ".$s." shield!"]
		]);
	}
}
?>
