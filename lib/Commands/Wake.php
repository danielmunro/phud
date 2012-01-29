<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor,
	\Mechanics\Server,
	\Mechanics\Ability\Ability,
	\Mechanics\Command\Command;

class Wake extends Command
{
	protected $dispositions = [Actor::DISPOSITION_SLEEPING];

	protected function __construct()
	{
		self::addAlias('wake', $this);
	}

	public function perform(Actor $actor, $args = array())
	{
		if($actor->getDisposition() === Actor::DISPOSITION_STANDING) {
			return Server::out($actor, "You are already awake.");
		}

		if(array_key_exists('sleep', $actor->getAffects())) {
			return Server::out($actor, "You can't wake up!");
		}
		
		Server::out($actor, "You wake up and stand up.");
		$actor->getRoom()->announce($actor, ucfirst($actor)." wakes up and stands up.");
		$actor->setDisposition(Actor::DISPOSITION_STANDING);
	}

}
?>
