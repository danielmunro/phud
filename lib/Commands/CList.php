<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Actor,
	\Mechanics\Command\User as cUser,
	\Living\User as lUser,
	\Living\Shopkeeper as lShopkeeper;

class CList extends cUser
{
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	protected function __construct()
	{
		self::addAlias('list', $this);
	}

	public function perform(lUser $user, $args = array())
	{
		if(sizeof($args) == 3) {
			$target = $user->getRoom()->getActorByInput($args);
		}
		else {
			foreach($user->getRoom()->getActors() as $potential_target) {
				if($potential_target instanceof lShopkeeper) {
					$target = $potential_target;
					break;
				}
			}
		}
		
		if(!isset($target)) {
			return Server::out($user, "They are not here.");
		}
		
		if(!($target instanceof lShopkeeper)) {
			return Server::out($user, "They are not selling anything.");
		}
		
		Say::perform($target, $target->getListItemMessage());
		Server::out($user, $target->displayContents(true));
	}
}
?>
