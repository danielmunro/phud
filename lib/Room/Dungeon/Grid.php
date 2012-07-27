<?php
namespace Phud\Room\Dungeon;
use Phud\Room\Direction;

class Grid extends Dungeon
{
	protected $dir_count = [];

	public function __construct($properties = [], &$rooms_left = 0, $depth = 0, &$exit = null)
	{
		foreach(Direction::getDirections() as $d) {
			$k = $d[0].'_count';
			$count = array_key_exists($k, $properties) ? $properties[$k] : 0;
			unset($properties[$k]);
			$this->dir_count[$d] = $count;
		}
		parent::__construct($properties, $rooms_left, $depth, $exit);
	}

	public function buildOut(&$inf, $depth = 0)
	{
		foreach($this->dir_count as $dir => $amount) {
			if($amount) {
				$p = $this->initializing_properties;
				unset($p['north'], $p['south'], $p['east'], $p['west'], $p['up'], $p['down'], $p['id']);
				foreach($this->dir_count as $d => $a) {
					$k = $d[0].'_count';
					$p[$k] = $a;
					if($dir == $d) {
						$p[$k]--;
					}
				}
				$new_room = new static($p, $this->rooms_left, $this->depth, $this->exit);
				$this->setDirection($dir, $new_room);
				$new_room->setDirection(Direction::getReverse($dir), $this);
			}
		}
	}

	public function isStillBuilding($inf)
	{
		foreach(Direction::getDirections() as $d) {
			if($this->dir_count[$d] > 0) {
				return true;
			}
		}
		return false;
	}
}
?>
