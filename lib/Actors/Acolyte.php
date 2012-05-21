<?php
namespace Phud\Actors;
use Phud\Server;

class Acolyte extends Mob
{
	public function practice(User $user, $proficiency)
	{
		if($user->getProficiencyIn($proficiency) < $this->getProficiencyIn($proficiency)) {
			return Server::out($user, $this." has taught you everything they know.");
		}
		if(!$user->getPractices()) {
			return Server::out($user, "You don't have any more practices to do that.");
		}

		$user->decreasePractices();
		$user->improveProficiency($proficiency);
		return Server::out($user, "Your ".$proficiency." increases!");
	}
}
?>
