<?php
namespace Phud;

class Dungeon extends Room
{
	protected static $rooms = [];
	protected $rooms_left = 0;
	protected $depth = 0;
	protected $exit = 0;
	protected $n_prob = 0.5;
	protected $s_prob = 0.5;
	protected $e_prob = 0.5;
	protected $w_prob = 0.5;
	protected $u_prob = 0.5;
	protected $d_prob = 0.5;

	public function __construct($properties = [], &$rooms_left = 0, $depth = 0, &$exit = null)
	{
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

	public function setup()
	{
		static::$rooms[$this->short][] = $this;
		while($this->rooms_left) {
			$this->buildOut($this->rooms_left, $this->depth, $this->exit);
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

	public function buildOut(&$rooms_left, $depth, &$exit)
	{
		$dirs = [];
		foreach(Direction::getDirections() as $dir) {
			$dirs[$dir] = $this->{$dir[0].'_prob'};
		}
		uasort($dirs, function() { return round(rand(0, 1)); });
		foreach($dirs as $dir => $probability) {
			if($rooms_left <= 0) {
				return;
			}
			$r = $this->directions[$dir];
			if($r instanceof static && $r->memberOf($this)) {
				$r->buildOut($rooms_left, $depth, $exit);
			} else if(empty($r) && chance() < $probability) {
				$rooms_left--;
				$p = $this->initializing_properties;
				unset($p['north'], $p['south'], $p['east'], $p['west'], $p['up'], $p['down'], $p['id']);
				$p[Direction::getReverse($dir)] = $this;
				$p['area'] = $this->area;
				$r = new static($p, $rooms_left, $depth+1, $exit);
				$this->directions[$dir] = $r;
				static::$rooms[$this->short][] = $r;
			}
		}
	}

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
