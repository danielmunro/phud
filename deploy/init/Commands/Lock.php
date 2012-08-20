<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Items\Key;

class Lock extends Command
{
	protected $alias = 'lock';
	protected $min_argument_count = 1;
	protected $min_argument_fail = "Lock what?";
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, Door $door)
	{
		$d = $door->getDisposition();
		if($d === 'open') {
			return $actor->notify(ucfirst($door).' is open.');
		} else if($d === 'locked') {
			return $actor->notify(ucfirst($door).' is already locked.');
		} else if($d === 'closed') {
			$keys = $actor->getManyItemsByInput('key');
			foreach($keys as $key) {
				if($key instanceof Key && $key->getDoorID() == $door->getID()) {
					$door->setDisposition('locked');
					return $actor->notify("You lock ".$door.".");
				}
			}
		}
		return $actor->notify("You don't have the key!");
	}

	protected function getArgumentsFromHints(Actor $actor, $args)
	{
		return [(new Arguments\Door())->parse($actor, $args[1])];
	}
}
