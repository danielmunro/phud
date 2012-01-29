<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor,
	\Mechanics\Room as mRoom,
	\Mechanics\Command\Command;

class Recall extends Command
{
	protected $alias = 'recall';

	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $args = [])
	{
		$actor->setRoom(mRoom::find(1));
	}
}
?>
