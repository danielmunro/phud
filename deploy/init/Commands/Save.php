<?php
namespace Phud\Commands;
use Phud\Server,
	Phud\Actors\User as aUser;

class Save extends User
{
	protected $alias = 'save';
	
	public function perform(aUser $user, $args = [])
	{
		if(method_exists($user, 'save')) {
			$user->save();
			Server::out($user, 'Done.');
		}
		else {
			return Server::out($user, 'Cannot do that.');
		}
	}
}
?>
