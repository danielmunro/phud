<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Actors\User as aUser,
	Phud\Room\Room,
	Phud\Room\Door,
	Phud\Room\Direction;

class Move_Direction extends Command
{
	protected $dispositions = [Actor::DISPOSITION_STANDING];
	protected $alias = [
		['north', 11],
		['south', 11],
		['east', 11],
		['west', 11],
		['up', 11],
		['down', 11]
	];

	public function perform(Actor $actor, $args, $hints)
	{
		if($actor->getTarget()) {
			return $actor->notify('You cannot leave a fight!');
		}

		$direction = $hints[0]->parse($actor, $args[0]);

		$room = $actor->getRoom()->getDirection($direction);
		if($room instanceof Room) {
			$doors = $actor->getRoom()->getDoors();
			if(isset($doors[$direction]) && $doors[$direction]->getDisposition() !== Door::DISPOSITION_OPEN) {
				return $actor->notify(ucfirst($doors[$direction]).' is not open.');
			}
			$movement_cost = 1;
			$actor->fire('moved', $movement_cost, $room);
			if($actor->getAttribute('movement') >= $movement_cost) {
				$actor->modifyAttribute('movement', -($movement_cost));
				$actor->getRoom()->announce([
					['actor' => $actor, 'message' => ''],
					['actor' => '*', 'message' => ucfirst($actor).' '.$actor->getRace()->getMoveVerb().' '.$direction.'.']
				]);
				$actor->setRoom($room);
				if($actor instanceof aUser) {
					Command::lookup('look')->perform($actor);
				}
				$actor->getRoom()->announce([
					['actor' => $actor, 'message' => ''],
					['actor' => '*', 'message' => ucfirst($actor).' has arrived.']
				]);
				return;
			}
			$actor->notify('You are too exhausted.');
		} else {
			$actor->notify('Alas, you cannot go that way.');
		}
	}

	public function getArgumentHints()
	{
		return [
			new Arguments\Direction()
		];
	}
}
