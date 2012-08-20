<?php
namespace Phud\Commands;
use Phud\Server,
	Phud\Actors\User as lUser;

class Dungeon_Master extends User
{
	protected $alias = 'dungeon master';

	public function perform(lUser $user, $target)
	{
		$target->setDM(true);
		$target->notify("You are now the dungeon master.");
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		return [$actor];
	}
}
