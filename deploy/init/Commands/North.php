<?php
namespace Phud\Commands;
use Phud\Actors\Actor;

class North extends Move_Direction
{
	protected $alias = ['north', 11];

	public function perform(Actor $actor, $args = [])
	{
		parent::perform($actor, 'north');
	}
}
?>
