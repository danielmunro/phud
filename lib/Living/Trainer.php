<?php
namespace Living;
use \Living\User,
	\Mechanics\Server;

class Trainer extends Mob
{
	protected $alias = 'a generic trainer';
	protected $max_proficiency = 0;

	public function train(User $user, $stat)
	{
		if($user->getTrains()) {
			$user->decreaseTrains();
			$user->modifyAttribute($stat, 1);
			return Server::out($user, "Your ".$stat." increases!");
		}
		Server::out($user, "You don't have the trains to do that.");
	}
}
?>
