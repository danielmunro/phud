<?php
namespace Phud\Commands;
use Phud\Actors\Actor;

class West extends Move_Direction
{
	protected $alias = ['west', 11];

	public function perform(Actor $actor, $args = [])
	{
		parent::perform($actor, 'west');
	}
}
?>
