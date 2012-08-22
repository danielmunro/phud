<?php
namespace Phud\Room;
use Phud\Actors\Actor,
	Phud\Actors\User,
	Phud\Inventory,
	Phud\EasyInit,
	Phud\Identity,
	Phud\Interactive,
	Phud\Debug,
	\Exception;

class Room
{
	use Inventory, EasyInit, Identity, Interactive;

	protected static $instances = [];
	protected static $start_room = 0;
	protected $area = null;
	protected $visibility = 1;
	protected $movement_cost = 0;
	protected $actors = [];
	protected $doors = [];
	protected $directions = [];

	const PURGATORY_ROOM_ID = 900;

	public function __construct($properties = [])
	{
		foreach(Direction::getDirections() as $d) {
			$this->directions[$d] = isset($properties[$d]) ? $properties[$d] : null;
			unset($properties[$d]);
		}
		$this->initializeProperties($properties, [
				'actors' => function($room, $property, $value) {
				foreach($value as $actor) {
				$actor->setRoom($room);
				}
				},
				'door' => function($room, $property, $value) {
				list($direction, $door_id) = explode(' ', $value);
				$room->setDoor(Direction::getFullAlias($direction), Door::getByID($door_id));
				}
				]);
		if(isset(self::$identities[$this->id])) {
			throw new Exception("Room already exists for ID: ".$this->id);
		}
		self::$identities[$this->id] = $this;
		Debug::log("[info] creating room: ".$this->short." [".$this->id."]");
	}

	public static function setStartRoom($room_id)
	{
		self::$start_room = $room_id;
	}

	public static function getStartRoom()
	{
		return self::$start_room;
	}

	public function setDoor($direction, Door $door)
	{
		$this->doors[$direction] = $door;
	}

	public function buildDirections()
	{
		foreach($this->directions as $direction => $id) {
			if(is_numeric($id)) {
				$this->directions[$direction] = self::getByID($id);
			}
		}
	}

	public function setDirection($direction, self $value)
	{
		if(array_key_exists($direction, $this->directions)) {
			$this->directions[$direction] = $value;
		} else {
			Debug::log('[error] '.$direction.' is not a valid direction.');
		}
	}

	public function getDirection($direction)
	{
		if(array_key_exists($direction, $this->directions)) {
			return $this->directions[$direction];
		} else {
			Debug::log('[error] '.$direction.' is not a valid direction.');
		}
	}

	public function getDirections()
	{
		return $this->directions;
	}

	public function getDoors()
	{
		return $this->doors;
	}

	public function getDoorByInput($input)
	{
		return $this->getUsableByInput($this->doors, $input);
	}

	public function getVisibility()
	{
		return $this->visibility;
	}

	public function getMovementCost()
	{
		return $this->movement_cost;
	}

	public function getArea()
	{
		return $this->area;
	}

	public function setArea(Area $area)
	{
		$this->area = $area;
	}

	public function actorAdd(Actor $actor)
	{
		$this->actors[] = $actor;
		$actor->on(
				'moved',
				function($event, $actor, &$mvcost, $room) {
				$mvcost += $room->getMovementCost();
				}
				);
	}

	public function actorRemove(Actor $actor)
	{
		foreach($this->actors as $i => $a) {
			if($actor === $a) {
				unset($this->actors[$i]);
				return;
			}
		}
	}

	public function getActors()
	{
		return $this->actors;
	}

	public function announce($announcements)
	{
		$actors_announced = [];
		$general_announcement = '';
		foreach($announcements as $announcement) {
			if(isset($announcement['message'])) {
				if($announcement['actor'] === '*') {
					$general_announcement = $announcement['message'];
				} else {
					$actors_announced[] = $announcement['actor'];
					$announcement['actor']->notify($announcement['message']);
				}
			}
		}
		if($general_announcement) {
			foreach($this->actors as $actor) {
				if(!in_array($actor, $actors_announced)) {
					$actor->notify($general_announcement);
				}
			}
		}
	}
	
	public function getActorByInput($input)
	{
		return $this->getUsableByInput($this->actors, $input);
	}
	
	public function __toString()
	{
		return $this->short;
	}

	public function __sleep()
	{
		return ['id'];
	}
}
