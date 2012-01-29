<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor;

class Down extends Move_Direction
{
	protected $alias = ['down', 11];

	public function perform(Actor $actor, $args = [])
	{
		parent::perform($actor, [$actor->getRoom()->getDown(), 'down']);
	}
}
?>
