<?php
namespace Phud\Commands;
use Phud\Commands\User as cUser,
	Phud\Actors\User as aUser;

class Who extends cUser
{
	protected $alias = 'who';

	public function perform(aUser $user, $args = [])
	{
		$out = "Who list:\n";
		$n = 0;
		$user->getClient()->fire('who', $out, $n);
		$user->notify($out.$n.' player'.($n != 1 ? 's' : '').' found.');
	}
}
