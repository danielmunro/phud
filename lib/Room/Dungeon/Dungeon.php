<?php
namespace Phud\Room\Dungeon;
use Phud\Room\Room,
	Phud\Room\Direction;

abstract class Dungeon extends Room
{
	protected static $special_properties = ['exit'];
	protected static $inf = [];
	protected static $rooms = [];

	public function __construct($properties = [])
	{
		$this->setSpecialProperties($properties);
		if(empty($this->id)) {
			$i = rand();
			while(array_key_exists($i, self::$identities)) {
				$i = rand();
			}
			$this->id = $i;
		}
		parent::__construct($properties);
	}

	protected function setSpecialProperties(&$properties)
	{
		$i = $properties['short'];
		$special_properties = array_merge(self::$special_properties, static::$special_properties);
		foreach($special_properties as $special_property) {
			if(array_key_exists($special_property, $properties)) {
				static::$inf[$i][$special_property] = $properties[$special_property];
				unset($properties[$special_property]);
			}
		}
	}

	public function setup()
	{
		static::$rooms[$this->short][] = $this;
		while($this->isStillBuilding(static::$inf[$this->short])) {
			$this->buildOut(static::$inf[$this->short]);
		}
		if(array_key_exists('exit', static::$inf[$this->short])) {
			$dirs = Direction::getDirections();
			$dir_count = count($dirs)-1;
			$i = rand(0, $dir_count);
			$rand_dir = $dirs[$i];
			$rev_rand_dir = Direction::getReverse($rand_dir);
			$connect_room = static::getRandom($this->short);
			$exit_room = Room::getByID(static::$inf[$this->short]['exit']);
			while($connect_room->getDirection($rand_dir) || $exit_room->getDirection($rev_rand_dir)) {
				$i = rand(0, $dir_count);
				$rand_dir = $dirs[$i];
				$rev_rand_dir = Direction::getReverse($rand_dir);
			}
			$connect_room->setDirection($rand_dir, $exit_room);
			$exit_room->setDirection($rev_rand_dir, $connect_room);
		}
	}

	abstract public function buildOut(&$inf, $depth = 0);

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
