<?php
namespace Phud\Abilities;
use Phud\Actors\Actor;

class Enhanced_Damage extends Skill
{
	protected $alias = 'enhanced damage';
	protected $proficiency = 'melee';
	protected $required_proficiency = 35;
	protected $hard_modifier = ['str'];
	protected $event = 'damage modifier';

	public function initializeListener()
	{
		$this->listener = function($attacker, $victim, &$modifier) {
			if($this->perform($fighter)) {
				$v1 = $attacker->getAttribute('str') / 100;
				$modifier += rand($v1 / 2, $v1 * 1.25);
			}
		};
	}

	protected function applyCost(Actor $actor) {}

	protected function fail(Actor $actor) {}

	protected function success(Actor $actor) {}
}
?>
