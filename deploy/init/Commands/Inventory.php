<?php
namespace Phud\Commands;
use Phud\Actors\User as lUser;

class Inventory extends User
{
	protected $alias = 'inventory';

	public function perform(lUser $user, $args = [])
	{
		$user->notify("Your inventory:\r\n".$user->displayContents());
	}
}
