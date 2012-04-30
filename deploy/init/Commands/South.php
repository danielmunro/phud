<?php
namespace Phud\Commands;
use Phud\Actors\Actor;

class South extends Move_Direction
{
	protected $alias = ['south', 11];

	public function perform(Actor $actor, $args = [])
	{
		parent::perform($actor, 'south');
	}
}
?>
