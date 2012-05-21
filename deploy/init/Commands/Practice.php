<?php
namespace Phud\Commands;
use Phud\Actors\User as aUser,
	Phud\Actors\Acolyte;

class Practice extends User
{
	protected $alias = 'practice';
	protected $disposition = ['standing'];

	public function perform(aUser $user, $args = [])
	{
		if(!isset($args[1])) {
			return Server::out($user, "Practice list: (not implemented yet)");
		}
		if($args[1] == 'melee') {
			foreach($user->getRoom()->getActors() as $a) {
				if($a instanceof Acolyte) {
					$a->practice($user, $args[1]);
				}
			}
		}
	}
}
?>
