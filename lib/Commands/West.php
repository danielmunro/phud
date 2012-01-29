<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor;

class West extends Move_Direction
{
	protected $alias = ['west', 11];

	public function perform(Actor $actor, $args = [])
	{
		parent::perform($actor, [$actor->getRoom()->getWest(), 'west']);
	}
}
?>
