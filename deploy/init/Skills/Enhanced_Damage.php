<?php
namespace Phud\Abilities;
use Phud\Event\Subscriber,
	Phud\Event\Event,
	Phud\Actors\Actor,
	Phud\Server;

class Enhanced_Damage extends Skill
{
	protected $alias = 'enhanced damage';
	protected $proficiency = 'melee';
	protected $required_proficiency = 35;
	protected $hard_modifier = ['str'];

	public function getSubscriber()
	{
		return new Subscriber(
			Event::EVENT_DAMAGE_MODIFIER_ATTACKING,
			$this,
			function($subscriber, $fighter, $enh, $target, &$modifier, &$dam_roll) {
				$modifier += $this->perform($fighter);
			}
		);
	}

	protected function applyCost(Actor $actor) {}

	protected function fail(Actor $actor)
	{
		return 0;
	}

	protected function success(Actor $actor)
	{
		$v1 = $actor->getAttribute('str') / 100;
		return rand($v1 / 2, $v1 * 1.25);
	}
}
?>
