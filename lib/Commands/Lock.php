<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Server,
	Phud\Items\Key;

class Lock extends Command
{
	protected $alias = 'lock';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $args = [])
	{
		if(sizeof($args) < 2) {
			return Server::out($actor, 'Lock what?');
		}
		$door = $actor->getRoom()->getDoorByInput($args[1]);
		if($door) {
			$d = $door->getDisposition();
			if($d === 'open') {
				return Server::out($actor, ucfirst($door).' is open.');
			} else if($d === 'locked') {
				return Server::out($actor, ucfirst($door).' is already locked.');
			} else if($d === 'closed') {
				$keys = $actor->getManyItemsByInput('key');
				foreach($keys as $key) {
					if($key instanceof Key && $key->getDoorID() == $door->getID()) {
						$door->setDisposition('locked');
						return Server::out($actor, "You lock ".$door.".");
					}
				}
			}
			return Server::out($actor, "You don't have the key!");
		}
		Server::out($actor, "You don't see a door like that anywhere.");
	}
}
?>
