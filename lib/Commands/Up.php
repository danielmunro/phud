<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor;
	
class Up extends Move_Direction
{
	protected function __construct()
	{
		self::addAlias('up', $this, 11);
	}

	public function perform(Actor $actor, $args = [])
	{
		parent::perform($actor, [$actor->getRoom()->getUp(), 'up']);
	}
}
?>
