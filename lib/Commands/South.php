<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor;

class South extends Move_Direction
{
	protected $alias = ['south', 11];

	public function perform(Actor $actor, $args = [])
	{
		parent::perform($actor, [$actor->getRoom()->getSouth(), 'south']);
	}
}
?>
