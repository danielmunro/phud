<?php
namespace Phud;

class Dungeon extends Room
{
	protected static $rooms = [];
	protected $rooms_left = 0;
	protected $depth = 0;
	protected $exit = 0;

	public function __construct($properties = [], &$rooms_left = 0, $depth = 0, &$exit = null)
	{
		if(isset($properties['rooms'])) {
			$this->rooms_left = $properties['rooms'];
			unset($properties['rooms']);
		}
		if(isset($properties['exit'])) {
			$this->exit = $properties['exit'];
			unset($properties['exit']);
		}
		parent::__construct($properties);
	}

	public function setup()
	{
		static::$rooms[$this->area->getAlias()][] = $this;
		while($this->rooms_left > 0) {
			$this->buildOut($this->rooms_left, $this->depth, $this->exit);
		}
		$dirs = ['north', 'south', 'east', 'west', 'up', 'down'];
		$rand_dir = rand(0, 5);
		while($this->$dirs[$rand_dir] > 0) {
			$rand_dir = rand(0, 5);
		}
		$exit_room = Room::getByID($this->exit);
		$this->$dirs[$rand_dir] = $this->exit;
		$exit_room->setDirection(Room::getReverseDirection($dirs[$rand_dir]), $this->id);
		$this->exit = null;
	}

	public function buildOut(&$rooms_left, $depth, &$exit)
	{
		$dirs = ['north', 'south', 'east', 'west', 'up', 'down'];
		shuffle($dirs);
		foreach($dirs as $dir) {
			if($rooms_left <= 0) {
				return;
			}
			$r = null;
			if($this->$dir) {
				$r = Room::getByID($this->$dir);
			} else if(chance() < 50) {
				$rooms_left--;
				$r = new static([Room::getReverseDirection($dir) => $this->id, 'title' => $this->title, 'description' => $this->description, 'area' => $this->area], $rooms_left, $depth+1, $exit);
				$this->$dir = $r->getID();
				static::$rooms[$this->area->getAlias()][] = $this;
			}
			if($r instanceof static) {
				$r->buildOut($rooms_left, $depth, $exit);
			}
		}
	}

	public static function getRandomByArea(Area $area)
	{
		$i = array_rand(static::$rooms[$area->getAlias()]);
		return static::$rooms[$area->getAlias()][$i];
	}
}
?>
