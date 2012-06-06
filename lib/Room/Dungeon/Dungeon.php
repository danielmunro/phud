<?php
namespace Phud\Room\Dungeon;
use Phud\Room\Room,
	Phud\Room\Direction;

abstract class Dungeon extends Room
{
	protected $depth = 0;
	protected $exit = 0;
	protected static $special_properties = [];
	protected static $rooms = [];
	protected static $inf = [];

	public function __construct($properties = [], &$rooms_left = 0, $depth = 0, &$exit = null)
	{
		$this->setSpecialProperties($properties);
		if(isset($properties['rooms'])) {
			$this->rooms_left = $properties['rooms']-1;
			unset($properties['rooms']);
		}
		if(isset($properties['exit'])) {
			$this->exit = $properties['exit'];
			unset($properties['exit']);
		}
		if(empty($this->id)) {
			$i = rand();
			while(isset(self::$identities[$i])) {
				$i = rand();
			}
			$this->id = $i;
		}
		parent::__construct($properties);
	}

	protected function setSpecialProperties(&$properties)
	{
		$i = $properties['id'];
		foreach(static::$special_properties as $special_property) {
			if(array_key_exists($special_property, $properties)) {
				static::$inf[$i][$special_property] = $properties[$special_property];
				unset($properties[$special_property]);
			}
		}
	}

	public function setup()
	{
		static::$rooms[$this->short][] = $this;
		while($this->isStillBuilding(static::$inf[$this->id])) {
			$this->buildOut(static::$inf[$this->id]);
		}
		if($this->exit) {
			$dirs = Direction::getDirections();
			$i = rand(0, 5);
			$rand_dir = $dirs[$i];
			$rev_rand_dir = Direction::getReverse($rand_dir);
			$connect_room = static::getRandom($this->short);
			$exit_room = Room::getByID($this->exit);
			while($connect_room->getDirection($rand_dir) || $exit_room->getDirection($rev_rand_dir)) {
				$i = rand(0, 5);
				$rand_dir = $dirs[$i];
				$rev_rand_dir = Direction::getReverse($rand_dir);
			}
			$connect_room->setDirection($rand_dir, $exit_room);
			$exit_room->setDirection($rev_rand_dir, $connect_room);
			$this->exit = null;
		}
	}

	abstract public function buildOut(&$rooms_left, $depth, &$exit);

	abstract public function isStillBuilding($inf);

	public function memberOf(self $dungeon)
	{
		return $this->short === $dungeon->short;
	}

	public static function getRandom($short)
	{
		$i = array_rand(static::$rooms[$short]);
		return static::$rooms[$short][$i];
	}
}
?>
