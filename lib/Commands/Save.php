<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Actor,
	\Mechanics\Command\User as cUser,
	\Living\User,
	\Living\Mob as mMob;

class Save extends cUser
{
	protected $alias = 'save';
	
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
