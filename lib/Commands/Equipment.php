<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor,
	\Mechanics\Server,
	\Mechanics\Command\Command;

class Equipment extends Command
{
	protected function __construct()
	{
		self::addAlias('equipment', $this);
	}

	public function perform(Actor $actor, $args = array())
	{
		Server::out($actor, 'Your equipment:');
		Server::out($actor, $actor->getEquipped()->displayContents());		
	}
}
?>
