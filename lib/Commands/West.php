<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor;

class West extends Move_Direction
{
	protected function __construct()
	{
		self::addAlias('west', $this, 11);
	}

	public function perform(Actor $actor, $args = [])
	{
		parent::perform($actor, [$actor->getRoom()->getWest(), 'west']);
	}
}
?>
