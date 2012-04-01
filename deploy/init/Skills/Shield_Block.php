<?php
namespace Skills;
use Phud\Ability\Skill,
	Phud\Actor\Actor;

class Shield_Block extends Skill
{
	protected $alias = 'shield block';
	protected $proficiency = 'melee';
	protected $required_proficiency = 25;
	protected $normal_modifier = ['dex'];
	protected $hard_modifier = ['str'];
	
	public function getSubscriber()
	{
		return new Subscriber(
			Event::EVENT_MELEE_ATTACKED,
			$this,
			function($subscriber, $fighter, $ability, $attack_subscriber) {
				$ability->perform($fighter, [$attack_subscriber, $subscriber]);
			}
		);
	}

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
		$args[0]->suppress();
		$args[1]->satisfyBroadcast();
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
