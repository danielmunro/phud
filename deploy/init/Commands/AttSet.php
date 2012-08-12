<?php
namespace Phud\Commands;
use Phud\Actors\User as lUser;

class AttSet extends DM
{
	protected $alias = 'attset';
	
	public function perform(lUser $user, $args = [])
	{
		$object = $user->getRoom()->getActorByInput($args[1]);
		if(!$object) {
			$object = $user->getRoom()->getItemByInput($args[1]);
		}
		if(!$object) {
			$object = $user->getItemByInput($args[1]);
		}
		if(!$object) {
			return $user->notify("That doesn't seem to exist.");
		}

		if($object->setAttribute($args[2], $args[3])) {
			$user->notify("You set ".$object."'s ".$args[2]." to ".$args[3].".");
			if(method_exists($object, 'save')) {
				$object->save();
			}
		} else {
			$user->notify("They don't have that attribute.");
		}
	}
}
