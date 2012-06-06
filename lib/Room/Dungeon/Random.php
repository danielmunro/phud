<?php
namespace Phud\Room\Dungeon;
use Phud\Room\Direction;

class Random extends Dungeon
{
	protected $n_prob = 0.5;
	protected $s_prob = 0.5;
	protected $e_prob = 0.5;
	protected $w_prob = 0.5;
	protected $u_prob = 0.5;
	protected $d_prob = 0.5;
	protected static $special_properties = ['rooms', 'exit'];

	public function buildOut(&$dungeon_inf, $depth = 0)
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

	public function isStillBuilding($inf)
	{
		return $inf['rooms'] > 0;
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
