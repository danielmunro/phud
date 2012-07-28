<?php
namespace Phud\Abilities;
use Phud\Actors\Actor,
	Phud\Server,
	Phud\Affects\Affect;

class Bash extends Skill
{
	protected $alias = 'bash';
	protected $proficiency = 'melee';
	protected $required_proficiency = 20;
	protected $easy_modifier = ['str'];
	protected $needs_target = true;
	protected $is_offensive = true;
	protected $event = 'input';
	protected $delay = 2;

	protected function initializeListener()
	{
		$this->listener = $this->getInputListener();
	}

	protected function applyCost(Actor $actor)
	{
		$amount = min(20, 51 - $actor->getLevel());
		if($actor->getAttribute('movement') < $amount) {
			Server::out($actor, "You don't have the energy to bash them.");
			return false;
		}
		$actor->modifyAttribute('movement', -($amount));
	}

	protected function modifyRoll(Actor $actor)
	{
		$roll = 0;
		$roll -= $actor->getRace()->getSize() * 1.25;
		$roll += $actor->getTarget()->getRace()->getSize();
		$actor->getTarget()->fire('bash', $actor->getTarget(), $roll);
		return $roll;
	}

	protected function fail(Actor $actor)
	{
		$sexes = [Actor::SEX_MALE=>'him',Actor::SEX_FEMALE=>'her',Actor::SEX_NEUTRAL=>'it'];
		$s = $actor->getDisplaySex($sexes);
		$actor->getRoom()->announce([
			['actor' => $actor, 'message' => 'You fall flat on your face!'],
			['actor' => $actor->getTarget(), 'message' => ucfirst($actor)." tries to bash you but you evade their attack!"],
			['actor' => '*', 'message' => ucfirst($actor)." falls flat on ".$s." trying to bash ".$actor->getTarget()."!"]
		]);
	}

	protected function success(Actor $actor)
	{
		new Affect([
			'affect' => 'stun',
			'timeout' => 1,
			'apply' => $actor->getTarget()
		]);
		$sexes = [Actor::SEX_MALE=>'him',Actor::SEX_FEMALE=>'her',Actor::SEX_NEUTRAL=>'it'];
		$s = $actor->getTarget()->getDisplaySex($sexes);
		$actor->getRoom()->announce([
			['actor' => $actor, 'message' => "You slam into ".$actor->getTarget()." and send ".$s." flying!"],
			['actor' => $actor->getTarget(), 'message' => ucfirst($actor)." slams into you and sends you flying!"],
			['actor' => '*', 'message' => ucfirst($actor)." slams into ".$actor->getTarget()." and sends ".$s." flying!"]
		]);
	}
}
?>
