<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Server,
	Phud\Room,
	Phud\Event\Subscriber;

class Flee extends Command
{
	protected $alias = 'flee';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $fighter, $args = [], Subscriber $command_subscriber)
	{
		$target = $fighter->getTarget();
		if(!$target) {
			return Server::out($fighter, "Flee from who?");
		}
		$target->setTarget(null);
		$fighter->setTarget(null);
		
		$directions = array(
						'north' => $fighter->getRoom()->getNorth(),
						'south' => $fighter->getRoom()->getSouth(),
						'east' => $fighter->getRoom()->getEast(),
						'west' => $fighter->getRoom()->getWest(),
						'up' => $fighter->getRoom()->getUp(),
						'down' => $fighter->getRoom()->getDown());
		$direction = rand(0, sizeof($directions)-1);
		$directions = array_filter(
								$directions,
								function($d)
								{
									return $d instanceof Room;
								}
							);
		uasort(
			$directions,
			function($i)
			{
				return rand(0, 1);
			}
		);
		foreach($directions as $dir => $id)
		{
			$command = Command::lookup($dir);
			$command['lookup']->perform($fighter);
			Server::out($fighter, "You run scared!");
			return;
		}
	}
}
?>
