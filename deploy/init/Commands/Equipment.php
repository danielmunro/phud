<?php
namespace Phud\Commands;
use Phud\Actors\Actor;

class Equipment extends Command
{
	protected $alias = 'equipment';

	public function perform(Actor $actor, $args = [])
	{
		$actor->notify("Your equipment:\r\n".$actor->getEquipped()->displayContents());		
	}
}
