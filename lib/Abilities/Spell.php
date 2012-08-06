<?php
namespace Phud\Abilities;
use Phud\Actors\Actor,
	Phud\Actors\User,
	Phud\Instantiate;

abstract class Spell extends Ability
{
	use Instantiate;

	protected $initial_mana_cost = 50;
	protected $min_mana_cost = 15;

	public function getManaCost($proficiency)
	{
		$min = round(($this->initial_mana_cost - $proficiency) / 10) * 10;
		return max($min, $this->min_mana_cost);
	}

	protected function applyCost(Actor $actor)
	{
		$mana_cost = $this->getManaCost($actor->getProficiencyScore($this->proficiency));
		if($actor->getAttribute('mana') < $mana_cost) {
			if($actor instanceof User) {
				$actor->getClient()->writeLine("You lack the mana to do that.");
			}
			return false;
		}
		$actor->modifyAttribute('mana', -($mana_cost));
	}

	protected function fail(Actor $actor)
	{
		if($actor instanceof User) {
			$actor->getClient()->writeLine("You lost your concentration.");
		}
	}

	protected function determineTarget(Actor $actor, $args)
	{
		$s = sizeof($args);
		if($s === 2) {
			return $actor;
		} else if($s > 2) {
			// Spells, unlike skills, can target the actor performing the ability
			$target = $actor->getRoom()->getActorByInput($args[$s-1]);
			if($target) {
				return $target;
			}
		}
		return null;
	}
}
