<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor;

class East extends Move_Direction
{
	protected $alias = ['east', 11];

	public function perform(Actor $actor, $args = [])
	{
		parent::perform($actor, [$actor->getRoom()->getEast(), 'east']);
	}
}
?>
