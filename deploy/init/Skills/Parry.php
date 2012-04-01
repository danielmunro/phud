<?php
namespace Skills;
use Phud\Ability\Skill,
	Phud\Ability\Ability,
	\Mechanics\Event\Event,
	\Mechanics\Event\Subscriber,
	\Mechanics\Actor,
	\Mechanics\Equipped,
	\Mechanics\Race,
	\Mechanics\Server;

class Parry extends Skill
{
	protected $alias = 'parry';
	protected $proficiency = 'evasive';
	protected $required_proficiency = 25;
	protected $hard_modifier = ['dex'];

	public function getSubscriber()
	{
		return new Subscriber(
			Event::EVENT_MELEE_ATTACKED,
			$this,
			function($subscriber, $fighter, $ability, $attack_event) {
				$ability->perform($fighter, [$attack_event, $subscriber]);
			}
		);
	}
	
	protected function modifyRoll(Actor $actor)
	{
		$weapon = $actor->getEquipped()->getEquipmentByPosition(Equipped::POSITION_WIELD);
		if(!$weapon) {
			return -1;
		}
		return ($actor->getSize() - Race::SIZE_NORMAL) * 10;
	}

	protected function success(Actor $actor, Actor $target, $args)
	{
		$args[0]->suppress();
		$args[1]->satisfyBroadcast();
		$actor->getRoom()->announce2([
			['actor' => $actor, 'message' => "You parry ".$actor->getTarget()."'s attack!"],
			['actor' => $actor->getTarget(), 'message' => ucfirst($actor)." parries your attack!"],
			['actor' => '*', 'message' => ucfirst($actor)." parries ".$actor->getTarget()."'s attack!"]
		]);
	}
}
?>
