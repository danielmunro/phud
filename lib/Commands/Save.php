<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Actor,
	\Mechanics\Command\Command,
	\Living\User,
	\Living\Mob as mMob;

class Save extends Command
{

	protected function __construct()
	{
		self::addAlias('save', $this);
	}
	
	public function perform(Actor $actor, $args = array())
	{
		if(method_exists($actor, 'save')) {
			$actor->save();
			Server::out($actor, 'Done.');
		}
		else {
			return Server::out($actor, 'Cannot do that.');
		}
	}
}
?>
