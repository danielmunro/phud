<?php
namespace Phud\Commands;
use Phud\Door as mDoor,
	Phud\Actors\Actor,
	Phud\Items\Key;

class Unlock extends Command
{
	protected $alias = 'unlock';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $args = [])
	{
		if(sizeof($args) < 2) {
			return $actor->notify("Unlock what?");
		}
		$door = $actor->getRoom()->getDoorByInput($args[1]);
		if($door) {
			$d = $door->getDisposition();
			if($d === 'open') {
				return $actor->notify(ucfirst($door).' is already open.');
			} else if($d === 'closed') {
				return $actor->notify(ucfirst($door).' is unlocked.');
			} else if($d === 'locked') {
				$keys = $actor->getManyItemsByInput('key');
				foreach($keys as $key) {
					if($key instanceof Key && $key->getDoorID() == $door->getID()) {
						$door->setDisposition('closed');
						return $actor->notify("You unlock ".$door.".");
					}
				}
			}
			return $actor->notify("You don't have the key!");
		}
		$actor->notify("You don't see a door like that anywhere.");
	}
}
