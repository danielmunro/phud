<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Items\Container;

class Get extends Command
{
	protected $alias = 'get';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(Actor $actor, $args = [])
	{
		$s = sizeof($args);
		if($s === 2) {
			$item = $actor->getRoom()->getItemByInput($args[1]);
			if(!$item->getCanOwn()) {
				return $actor->notify("You cannot pick that up.");
			}
			$actor->getRoom()->removeItem($item);
			$actor->addItem($item);
			return $actor->notify('You get '.$item.'.');
		}
		else if($s > 2) {
			// getting something from somewhere
			$container = $actor->getRoom()->getContainerByInput($args[$s-1]);
			if(!$container) {
				$container = $actor->getContainerByInput($args[$s-1]);
			}
			if(!$container) {
				return $actor->notify("Nothing is there.");
			}
			
			if($args[1] == 'all') {
				foreach($container->getItems() as $item) {
					$item->transferOwnership($container, $actor);
					$actor->notify('You get '.$item.' from '.$container.'.');
				}
			} else {
				$item = $container->getItemByInput(implode(' ', array_slice($args, 1, $s-2)));
				if($item) {
					$item->transferOwnership($container, $actor);
					$actor->notify('You get '.$item.' from '.$container.'.');
				}
			}
			return;
		}
		return $actor->notify("You see nothing like that.");
	}
}
