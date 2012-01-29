<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Actor,
	\Mechanics\Command\Command;
	
class Sleep extends Command
{
	protected $alias = 'sleep';
	protected $dispositions = [Actor::DISPOSITION_SLEEPING];

	public function perform(Actor $actor, $args = array())
	{
		if($actor->getDisposition() === Actor::DISPOSITION_SLEEPING)
			return Server::out($actor, "You are already sleeping.");
		
		Server::out($actor, "You lie down and go to sleep.");
		$actor->getRoom()->announce($actor, ucfirst($actor)." lies down and goes to sleep.");
		$actor->setDisposition(Actor::DISPOSITION_SLEEPING);
	}

}
?>
