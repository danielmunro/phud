<?php
namespace Commands;
use \Mechanics\Actor,
	\Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Command\Command,
	\Items\Container,
	\Items\Item as iItem,
	\Mechanics\Item as mItem;

class Get extends Command
{
	protected $dispositions = array(Actor::DISPOSITION_STANDING, Actor::DISPOSITION_SITTING);

	protected function __construct()
	{
		self::addAlias('get', $this);
	}

	public function perform(Actor $actor, $args = array())
	{
	
		if(sizeof($args) === 2)
		{
			$item = $actor->getRoom()->getItemByInput($args[1]);
			$container = $actor->getRoom();
		}
		else
		{
			
			array_shift($args);
			
			// getting something from somewhere
			$container = $actor->getRoom()->getContainerByInput($args);
			if(!($container instanceof Container))
				$container = $actor->getContainerByInput($args);
			if(!($container instanceof Container))
				return Server::out($actor, "Nothing is there.");
			
			if($args[0] == 'all')
			{
				foreach($container->getItems() as $item)
				{
					$item->transferOwnership($container, $actor);
					Server::out($actor, 'You get '.$item.' from '.$container.'.');
				}
				return;
			}
			else
			{
			
				$item = $container->getItemByInput(array('', $args[0]));
			
				if($item instanceof iItem)
					$from = ' from ' . $container;
				else
					return Server::out($actor, "You see nothing like that.");
			}
		}
		
		if($item instanceof mItem)
		{
			if(!$item->getCanOwn())
				return Server::out($actor, "You cannot pick that up.");
			
			$container->removeItem($item);
			$actor->addItem($item);
			Server::out($actor, 'You get '.$item.(isset($from) ? $from : '') . '.');
		}
		else
		{
			Server::out($actor, 'You see nothing like that.');
		}
	
	}

}

?>
