<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Actor,
	\Mechanics\Command\Command;

class Say extends Command
{
	protected $alias = 'say';

	public function perform(Actor $actor, $args = array())
	{
		
		$actors = $actor->getRoom()->getActors();
		
		if(is_array($args))
		{
			array_shift($args);
			$message = implode(' ', $args);
		}
		else if(is_string($args))
			$message = $args;
		
		foreach($actors as $a)
			if($a->getAlias() == $actor->getAlias())
				Server::out($a, "You say, \"" . $message ."\"");
			else
				Server::out($a, ucfirst($actor) . " says, \"" . $message . "\"");
	}
}
?>
