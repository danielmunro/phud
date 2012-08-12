<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Actors\User as lUser,
	Phud\Actors\Shopkeeper as lShopkeeper;

class CList extends User
{
	protected $alias = 'list';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(lUser $user, $args = [])
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
			return $user->notify("They are not here.");
		}
		
		if(!($target instanceof lShopkeeper)) {
			return $user->notify("They are not selling anything.");
		}
		
		Say::perform($target, $target->getListItemMessage());
		$user->notify($target->displayContents(true));
	}
}
?>
