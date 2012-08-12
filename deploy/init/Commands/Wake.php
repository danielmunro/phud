<?php
namespace Phud\Commands;
use Phud\Actors\Actor;

class Wake extends Command
{
	protected $alias = 'wake';
	protected $dispositions = [
		Actor::DISPOSITION_SLEEPING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(Actor $actor, $args = [])
	{
		if($actor->getDisposition() === Actor::DISPOSITION_STANDING) {
			return $actor->notify("You are already awake.");
		}

		if(array_key_exists('sleep', $actor->getAffects())) {
			return $actor->notify("You can't wake up!");
		}
		
		$actor->notify("You wake up and stand up.");
		$actor->getRoom()->announce($actor, ucfirst($actor)." wakes up and stands up.");
		$actor->setFurniture(null);
		$actor->setDisposition(Actor::DISPOSITION_STANDING);
	}

}
