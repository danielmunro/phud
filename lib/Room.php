<?php
namespace Phud;
use Phud\Actors\Actor,
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
		if(empty($this->id)) {
			$i = rand();
			while(isset(self::$identities[$i])) {
				$i = rand();
			}
			$this->id = $i;
		}
		if(isset(self::$identities[$this->id])) {
			echo "Room already exists: ".$this->id."\n";
			die;
		}
		self::$identities[$this->id] = $this;
		Debug::log("Creating room: ".$this->short." [".$this->id."]");
	}

	public static function startBuildDirections()
	{
		self::getByID(self::$start_room)->buildDirections();
	}

	public function buildDirections()
	{
		foreach($this->directions as $direction => $room) {
			if(is_numeric($room)) {
				$this->directions[$direction] = self::getByID($room);
				$this->directions[$direction]->buildDirections();
			}
		}
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

	public function setDirection($direction, self $value)
	{
		if(isset($this->directions[$direction])) {
			$this->$direction = $value;
		} else {
			Debug::log($direction.' is not a valid direction.');
		}
	}

	public function getDirection($direction)
	{
		if(isset($this->directions[$direction])) {
			return $this->directions[$direction];
		} else {
			Debug::log($direction.' is not a valid direction.');
		}
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
		$key = array_search($actor, $this->actors);
		if($key === false) {
			Debug::log($actor.' is not here');
			return;
		}
		unset($this->actors[$key]);
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
			if($announcement['actor'] === '*') {
				$general_announcement = $announcement['message'];
			} else {
				$actors_announced[] = $announcement['actor'];
				Server::out($announcement['actor'], $announcement['message']);
			}
		}
		if($general_announcement) {
			foreach($this->actors as $actor) {
				if(!in_array($actor, $actors_announced)) {
					Server::out($actor, $general_announcement);
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
		return $this->alias;
	}

	public function __sleep()
	{
		return ['id'];
	}

}
?>
