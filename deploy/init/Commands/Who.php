<?php
namespace Phud\Commands;
use Phud\Server,
	Phud\Commands\User as cUser,
	Phud\Actors\User as aUser;

class Who extends cUser
{
	protected $alias = 'who';

	public function perform(aUser $user, $args = array())
	{
		$out = "Who list:\n";
		$n = 0;
		foreach(Server::instance()->getClients() as $c) {
			if($c->getUser()) {
				$u = $c->getUser();
				$out .= '['.$u->getLevel().' '.$u->getRace()->getAlias().'] '.$u."\n";
				$n++;
			}
		}
		Server::out($user, $out.$n.' player'.($n != 1 ? 's' : '').' found.');
	}
}
?>
