<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor;

class North extends Move_Direction
{
	protected $alias = ['north', 11];

	public function perform(Actor $actor, $args = [])
	{
		parent::perform($actor, [$actor->getRoom()->getNorth(), 'north']);
	}
}
?>
