<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor;

class Down extends Move_Direction
{
	protected function __construct()
	{
		self::addAlias('down', $this, 11);
	}

	public function perform(Actor $actor, $args = [])
	{
		parent::perform($actor, [$actor->getRoom()->getDown(), 'down']);
	}
}
?>
