<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor;

class South extends Move_Direction
{
	protected function __construct()
	{
		self::addAlias('south', $this, 11);
	}

	public function perform(Actor $actor, $args = [])
	{
		parent::perform($actor, [$actor->getRoom()->getSouth(), 'south']);
	}
}
?>
