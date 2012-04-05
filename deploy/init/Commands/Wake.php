<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Server;

class Wake extends Command
{
	protected $alias = 'wake';
	protected $dispositions = [
		Actor::DISPOSITION_SLEEPING,
		Actor::DISPOSITION_SITTING
	];

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
		$actor->setFurniture(null);
		$actor->setDisposition(Actor::DISPOSITION_STANDING);
	}

}
?>
