<?php
namespace Phud\Commands;
use Phud\Actors\User as aUser;

class Save extends User
{
	protected $alias = 'save';
	
	public function perform(aUser $user, $args = [])
	{
		if(method_exists($user, 'save')) {
			$user->save();
			$user->notify('Done.');
		} else {
			return $user->notify('Cannot do that.');
		}
	}
}
