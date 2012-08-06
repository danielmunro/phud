<?php
namespace Phud\Actors;
use Phud\Attributes;

class Trainer extends Mob
{
	public function train(User $user, $stat)
	{
		$c = $user->getClient();
		if($user->getUnmodifiedAttribute($stat) < Attributes::MAX_STAT) {
			return $c->writeLine("That stat is maxed.");
		}

		if(!$user->getTrains()) {
			return $c->writeLine("You don't have the trains to do that.");
		}

		$user->decreaseTrains();
		$user->modifyAttribute($stat, 1);
		return $c->writeLine("Your ".$stat." increases!");
	}
}
