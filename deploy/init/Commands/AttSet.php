<?php
namespace Phud\Commands;
use Phud\Server,
	Phud\Actors\User as lUser;

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
			return Server::out($user, "That doesn't seem to exist.");
		}

		if($object->setAttribute($args[2], $args[3])) {
			Server::out($user, "You set ".$object."'s ".$args[2]." to ".$args[3].".");
			if(method_exists($object, 'save')) {
				$object->save();
			}
		} else {
			Server::out($user, "They don't have that attribute.");
		}
	}
}
?>
