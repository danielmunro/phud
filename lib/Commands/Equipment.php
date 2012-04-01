<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Server;

class Equipment extends Command
{
	protected $alias = 'equipment';

	public function perform(Actor $actor, $args = array())
	{
		Server::out($actor, 'Your equipment:');
		Server::out($actor, $actor->getEquipped()->displayContents());		
	}
}
?>
