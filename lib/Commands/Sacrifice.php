<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor,
	\Items\Item as mItem,
	\Mechanics\Server,
	\Mechanics\Command\Command,
	\Items\Corpse,
	\Living\User as lUser,
	\Living\Mob as lMob;

class Sacrifice extends Command
{
	protected $alias = 'sacrifice';

	public function perform(Actor $actor, $args = array())
	{
		$item = $actor->getRoom()->getItemByInput($args[1]);
		
		if($item instanceof mItem)
		{
			$actor->getRoom()->removeItem($item);
			$copper = max(1, $item->getLevel()*3);
			if(!($item instanceof Corpse))
				$copper = min($copper, $item->getValue());
			Server::out($actor, "Mojo finds ".$item." pleasing and rewards you.");
			$actor->getRoom()->announce($actor, $actor." sacrifices ".$item." to Mojo.");
			$actor->addCopper($copper);
			return;
		}
		Server::out($actor, "You can't find that.");
	}
}
?>
