<?php
namespace Phud\Actors;
use Phud\Server, Phud\Attributes;

class Trainer extends Mob
{
	public function train(User $user, $stat)
	{
		if($user->getUnmodifiedAttribute($stat) < Attributes::MAX_STAT) {
			return Server::out($user, "That stat is maxed.");
		}

		if(!$user->getTrains()) {
			return Server::out($user, "You don't have the trains to do that.");
		}

		$user->decreaseTrains();
		$user->modifyAttribute($stat, 1);
		return Server::out($user, "Your ".$stat." increases!");
	}
}
?>
