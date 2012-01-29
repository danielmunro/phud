<?php
namespace Commands;
use \Mechanics\Actor,
	\Mechanics\Server,
	\Mechanics\Debug,
	\Mechanics\Alias,
	\Mechanics\Item as mItem,
	\Items\Drink as iDrink,
	\Mechanics\Command\User as cUser;

class Drink extends cUser
{

	protected $alias = 'drink';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];
	
	public function perform(Actor $actor, $args = [])
	{
		$drinkable = implode(' ', array_slice($args, 1));
		$item = null;
		if($drinkable) {
			$item = $actor->getItemByInput($drinkable);
			if(!$item) {
				$item = $actor->getRoom()->getItemByInput($drinkable);
			}
		}
		if(!$item) {
			$items = array_merge($actor->getItems(), $actor->getRoom()->getItems());
			foreach($items as $i) {
				if($i instanceof iDrink) {
					$item = $i;
					break;
				}
			}
		}
		
		if(!($item instanceof mItem))
			return Server::out($actor, "Nothing like that is here.");
		
		if(!($item instanceof iDrink))
			return Server::out($actor, "You can't drink that!");
		
		if($item->drink($actor)) {
			Server::out($actor, "You drink ".$item->getContents()." from ".$item.".");
		}
	}
}
?>
