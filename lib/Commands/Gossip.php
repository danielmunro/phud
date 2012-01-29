<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Command\User,
	\Living\User as lUser;

class Gossip extends User
{
	protected $alias = 'gossip';

	public function perform(lUser $user, $args = array())
	{
		if(is_array($args))
		{
			array_shift($args);
			$message = implode(' ', $args);
		}
		else
			$message = $args;
	
		$actors = lUser::getInstances();
		
		foreach($actors as $a)
			if($actor->getAlias() == $a->getAlias())
				Server::out($a, "You gossip, \"" . $message . "\"\n\n" . $a->prompt(), false);
			else
				Server::out($a, $a->getAlias(true) . " gossips, \"" . $message . "\"\n\n" . $a->prompt(), false);
	}
}
?>
