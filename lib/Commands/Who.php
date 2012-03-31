<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Command\User as cUser,
	\Living\User as lUser;

class Who extends cUser
{
	protected $alias = 'who';

	public function perform(lUser $user, $args = array())
	{
		$out = "Who list:\n";
		$n = 0;
		foreach(Server::instance()->getClients() as $c) {
			if($c->getUser()) {
				$u = $c->getUser();
				$out .= '['.$u->getLevel().' '.$u->getRace()['alias'].'] '.$u."\n";
				$n++;
			}
		}
		Server::out($user, $out.$n.' player'.($n != 1 ? 's' : '').' found.');
	}
}
?>
