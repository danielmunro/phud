<?php
namespace Mechanics;
use \Mechanics\Event\Subscriber,
	\Mechanics\Event\Event,
	\Exception;

class Room
{
	use Usable, Inventory, EasyInit;

	protected static $instances = [];
	protected $id = '';
	protected $title = 'Generic room';
	protected $description = 'A nondescript room.';
	protected $north = '';
	protected $south = '';
	protected $east = '';
	protected $west = '';
	protected $up = '';
	protected $down = '';
	protected $doors = ['north' => null, 'south' => null, 'east' => null, 'west' => null, 'up' => null, 'down' => null];
	protected $area = '';
	protected $visibility = 1;
	protected $movement_cost = 0;
	protected $_subscriber_movement = null;
	protected $persistable_list = 'rooms';
	protected $actors = [];
	protected static $start_room = 0;

	const PURGATORY_ROOM_ID = 5;

	public function __construct($properties = [])
	{
		$this->_subscriber_movement = new Subscriber(
			Event::EVENT_MOVED,
			$this,
			function($subscriber, $broadcaster, $room, &$movement_cost) {
				$movement_cost += $room->getMovementCost();
			}
		);
		$this->initializeProperties($properties, [
			'actors' => function($room, $property, $value) {
				foreach($value as $actor) {
					$actor->setRoom($room);
				}
			}
		]);
		if(empty($this->id) || isset(self::$instances[$this->id])) {
			$room = self::$instances[$this->id];
			if($room) {
				echo "Room already exists for ID (".$this->id.") -> ".$room->getTitle();
			} else {
				echo "Room ID is empty for: \n\n";
				var_dump($properties);
			}
			die;
		}
		self::$instances[$this->id] = $this;
	}

	public static function setStartRoom($room_id)
	{
		self::$start_room = $room_id;
	}

	public static function getStartRoom()
	{
		return self::$start_room;
	}

	public function getVisibility()
	{
		return $this->visibility;
	}
	
	public function getId()
	{
		return $this->id;
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

	private function getDirection($direction_str, $direction)
	{
		$door = $this->getDoor($direction_str);
		if($door instanceof Door && $door->getDisposition() !== Door::DISPOSITION_OPEN) {
			return -1;
		}
		if(is_numeric($direction) && $direction > -1 || $direction) {
			$direction = self::find($direction);
		}
		return $direction;
	}
	
	public function getDoor($direction)
	{
		if(isset($this->doors[$direction]))
			return $this->doors[$direction];
		return null;
	}
	
	public function getDoors()
	{
		return $this->doors;
	}
	
	public function getDoorByInput($input)
	{
		return $this->getUsableNounByInput($doors, $input);
	}
	
	public function getNorth()
	{
		return $this->getDirection('north', $this->north);
	}

	public function getSouth()
	{
		return $this->getDirection('south', $this->south);
	}

	public function getEast()
	{
		return $this->getDirection('east', $this->east);
	}

	public function getWest()
	{
		return $this->getDirection('west', $this->west);
	}

	public function getUp()
	{
		return $this->getDirection('up', $this->up);
	}

	public function getDown()
	{
		return $this->getDirection('down', $this->down);
	}

	public function getArea()
	{
		return $this->area;
	}

	public function actorAdd(Actor $actor)
	{
		Debug::log($actor.' is arriving to '.$this.' ('.$this->getID().')');
		$this->actors[] = $actor;
		$actor->addSubscriber($this->_subscriber_movement);
	}

	public function actorRemove(Actor $actor)
	{
		Debug::log($actor.' is leaving '.$this.' ('.$this->getID().')');
		$key = array_search($actor, $this->actors);
		if($key === false) {
			Debug::log($actor.' is not here');
			return;
		}
		$actor->removeSubscriber($this->_subscriber_movement);
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
		if(isset(self::$instances[$id]) && self::$instances[$id] instanceof self) {
			return self::$instances[$id];
		}
		$dbr = Dbr::instance();
		$properties = unserialize($dbr->get($id));
		if(empty($properties)) {
			$properties = ['id' => $id];
		}
		return new self($properties);
	}
	
	public static function getDirectionStr($dir)
	{
		switch($dir)
		{
			case strpos('north', $dir) === 0: return 'north';
			case strpos('south', $dir) === 0: return 'south';
			case strpos('east', $dir) === 0: return 'east';
			case strpos('west', $dir) === 0: return 'west';
			case strpos('up', $dir) === 0: return 'up';
			case strpos('down', $dir) === 0: return 'down';
			default: return false;
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
}
?>
