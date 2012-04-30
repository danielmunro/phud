<?php
namespace Phud\Commands;
use Phud\Actors\Actor;
	
class Up extends Move_Direction
{
	protected $alias = ['up', 11];

	public function perform(Actor $actor, $args = [])
	{
		parent::perform($actor, 'up');
	}
}
?>
