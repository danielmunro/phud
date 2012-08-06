<?php
namespace Phud\Actors;

class Acolyte extends Mob
{
	public function practice(User $user, $proficiency)
	{
		$user_prof = $user->getProficiencyScore($proficiency);
		$c = $user->getClient();
		if($user_prof === -1) {
			return $c->writeLine("You cannot practice that.");
		}
		if($user_prof < $this->getProficiencyScore($proficiency)) {
			return $c->writeLine($this." has taught you everything they know.");
		}
		if(!$user->getPractices()) {
			return $c->writeLine("You don't have any more practices to do that.");
		}

		$user->decreasePractices();
		$user->improveProficiency($proficiency);
		return $c->writeLine("Your ".$proficiency." increases!");
	}
}
