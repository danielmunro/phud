<?php
namespace Phud\Commands;
use Phud\Actors\User as aUser;

class Save extends User
{
	protected $alias = 'save';
	
	public function perform(aUser $user)
	{
		$user->save();
		$user->notify("Saved.");
	}
}
