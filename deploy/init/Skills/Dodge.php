<?php
namespace Skills;
use Phud\Ability\Ability,
	Phud\Ability\Skill,
	\Mechanics\Actor,
	\Mechanics\Server,
	\Mechanics\Race;

class Dodge extends Skill
{
	protected $alias = 'dodge';
	protected $proficiency = 'evasive';
	protected $required_proficiency = 25;
	protected $easy_modifier = ['dex'];

	public function getSubscriber()
	{
		return new Subscription(
			Event::EVENT_MELEE_ATTACKED,
			$this,
			function($subscriber, $fighter, $ability, $attack_event) {
				$ability->perform($fighter, [$attack_event, $subscriber]);
			}
		);
	}

	public function modifyRoll(Actor $actor)
	{
		return ($actor->getSize() - Race::SIZE_NORMAL) * 10;
	}

	protected function success(Actor $actor, Actor $target, $args)
	{
		$args[0]->suppress();
		$args[1]->satisfyBroadcast();
		$actor->getRoom()->announce2([
			['actor' => $actor, 'message' => "You dodge ".$target."'s attack."],
			['actor' => $target, 'message' => $actor." dodges your attack."],
			['actor' => '*', 'message' => $actor." dodges ".$target."'s attack."]
		]);
	}
}
?>
