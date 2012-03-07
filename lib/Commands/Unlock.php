<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Door as mDoor,
	\Mechanics\Actor,
	\Mechanics\Server,
	\Mechanics\Command\Command,
	\Items\Key;

class Unlock extends Command
{
	protected $alias = 'unlock';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $args = [])
	{
		if(sizeof($args) < 2) {
			return Server::out($actor, "Unlock what?");
		}
		$door = $actor->getRoom()->getDoorByInput($args[1]);
		if($door) {
			$d = $door->getDisposition();
			if($d === 'open') {
				return Server::out($actor, ucfirst($door).' is already open.');
			} else if($d === 'closed') {
				return Server::out($actor, ucfirst($door).' is unlocked.');
			} else if($d === 'locked') {
				$keys = $actor->getManyItemsByInput('key');
				foreach($keys as $key) {
					if($key instanceof Key && $key->getDoorID() == $door->getID()) {
						$door->setDisposition('closed');
						return Server::out($actor, "You unlock ".$door.".");
					}
				}
			}
			return Server::out($actor, "You don't have the key!");
		}
		Server::out($actor, "You don't see a door like that anywhere.");
	}
}
?>
