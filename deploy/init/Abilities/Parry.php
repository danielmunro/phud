<?php
namespace Phud\Abilities;
use Phud\Actors\Actor,
	Phud\Equipped,
	Phud\Race;

class Parry extends Skill
{
	protected $alias = 'parry';
	protected $proficiency = 'evasive';
	protected $required_proficiency = 25;
	protected $hard_modifier = ['dex'];
	protected $event = 'attacked';

	public function initializeListener()
	{
		$this->listener = function($fighter) {
			if($ability->perform($fighter)) {
			}
		};
	}
	
	protected function modifyRoll(Actor $actor)
	{
		$weapon = $actor->getEquipped()->getEquipmentByPosition(Equipped::POSITION_WIELD);
		if(!$weapon) {
			return -1;
		}
		return ($actor->getRace()->getSize() - Race::SIZE_NORMAL) * 10;
	}

	protected function success(Actor $actor, Actor $target)
	{
		$actor->getRoom()->announce2([
			['actor' => $actor, 'message' => "You parry ".$actor->getTarget()."'s attack!"],
			['actor' => $actor->getTarget(), 'message' => ucfirst($actor)." parries your attack!"],
			['actor' => '*', 'message' => ucfirst($actor)." parries ".$actor->getTarget()."'s attack!"]
		]);
	}
}
?>
