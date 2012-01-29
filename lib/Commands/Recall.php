<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor,
	\Mechanics\Room as mRoom,
	\Mechanics\Command\Command;

class Recall extends Command
{

	protected $dispositions = array(Actor::DISPOSITION_STANDING);

	protected function __construct()
	{
		self::addAlias('recall', $this);
	}

	public function perform(Actor $actor, $args = array())
	{
		$actor->setRoom(mRoom::find(1));
	}
}
?>
