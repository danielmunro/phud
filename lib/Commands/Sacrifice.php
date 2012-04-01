<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Items\Item as mItem,
	Phud\Items\Corpse,
	Phud\Server;

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
			$actor->modifyCurrency('copper', $copper);
			return;
		}
		Server::out($actor, "You can't find that.");
	}
}
?>
