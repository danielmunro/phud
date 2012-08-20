<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Items\Key;

class Unlock extends Command
{
	protected $alias = 'unlock';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, Door $door)
	{
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
		$actor->notify("You don't have the key!");
	}

	protected function getArgumentsFromHints(Actor $actor, $args)
	{
		return [(new Arguments\Door())->parse($actor, $args[1])];
	}
}
