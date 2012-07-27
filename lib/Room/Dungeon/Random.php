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
	protected static $special_properties = ['rooms'];

	public function buildOut(&$inf, $depth = 0)
	{
		$dirs = [];
		foreach(Direction::getDirections() as $dir) {
			$dirs[$dir] = $this->{$dir[0].'_prob'};
		}
		uasort($dirs, function() { return round(rand(0, 1)); });
		foreach($dirs as $dir => $probability) {
			if($inf['rooms'] <= 0) {
				return;
			}
			$r = $this->directions[$dir];
			if($r instanceof static && $r->memberOf($this)) {
				$r->buildOut($inf, $depth+1);
			} else if(empty($r) && chance() < $probability) {
				$inf['rooms']--;
				$p = $this->initializing_properties;
				unset($p['north'], $p['south'], $p['east'], $p['west'], $p['up'], $p['down'], $p['id']);
				$p[Direction::getReverse($dir)] = $this;
				$p['area'] = $this->area;
				$r = new static($p);
				$this->directions[$dir] = $r;
				static::$rooms[$this->short][] = $r;
			}
		}
	}

	public function isStillBuilding($inf)
	{
		return $inf['rooms'] > 0;
	}
}
?>
