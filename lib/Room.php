<?php
namespace Phud;
use Phud\Actors\Actor,
	\Exception;

class Room
{
	use Usable, Inventory, EasyInit, Identity;

	protected static $instances = [];
	protected $title = 'Generic room';
	protected $description = 'A nondescript room.';
	protected $north = '';
	protected $south = '';
	protected $east = '';
	protected $west = '';
	protected $up = '';
	protected $down = '';
	protected $area = '';
	protected $visibility = 1;
	protected $movement_cost = 0;
	protected $actors = [];
	protected $doors = [];
	protected static $start_room = 0;

	const PURGATORY_ROOM_ID = 900;

	public function __construct($properties = [])
	{
		$this->initializeProperties($properties, [
			'actors' => function($room, $property, $value) {
				foreach($value as $actor) {
					$actor->setRoom($room);
				}
			},
			'door' => function($room, $property, $value) {
				list($direction, $door_id) = explode(' ', $value);
				$room->setDoor(Room::getFullDirectionAlias($direction), Door::getByID($door_id));
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

	public function setDirection($direction, $value)
	{
		$this->$direction = $value;
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
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function getDescription()
	{
		return $this->description;
	}

	public function getMovementCost()
	{
		return $this->movement_cost;
	}

	public function getNorth()
	{
		return $this->north;
	}

	public function getSouth()
	{
		return $this->south;
	}

	public function getEast()
	{
		return $this->east;
	}

	public function getWest()
	{
		return $this->west;
	}

	public function getUp()
	{
		return $this->up;
	}

	public function getDown()
	{
		return $this->down;
	}

	public function getArea()
	{
		return $this->area;
	}

	public function actorAdd(Actor $actor)
	{
		Debug::log($actor.' is arriving to '.$this.' ('.$this->getID().')');
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
		Debug::log($actor.' is leaving '.$this.' ('.$this->getID().')');
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
	
	public static function find($id)
	{
		$room = static::getByID($id);
		if($room) {
			return $room;
		}
	}
	
	public static function getReverseDirection($direction)
	{
		if(strpos('north', $direction) === 0)
			return 'south';
		if(strpos('south', $direction) === 0)
			return 'north';
		if(strpos('east', $direction) === 0)
			return 'west';
		if(strpos('west', $direction) === 0)
			return 'east';
		if(strpos('up', $direction) === 0)
			return 'down';
		if(strpos('down', $direction) === 0)
			return 'up';
	}

	public function __toString()
	{
		return $this->title;
	}

	public function __sleep()
	{
		return ['id'];
	}

	public static function getFullDirectionAlias($dir)
	{
		foreach(['north', 'south', 'east', 'west', 'up', 'down'] as $direction) {
			if(strpos($direction, $dir) === 0) {
				return $direction;
			}
		}
	}
}
?>
