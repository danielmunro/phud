<?php
namespace Phud;

class Dungeon extends Room
{
	public function __construct($properties = [], &$rooms_left = 0, $depth = 0)
	{
		if(isset($properties['rooms'])) {
			$rooms_left = $properties['rooms'];
			unset($properties['rooms']);
		}
		parent::__construct($properties);
		if($rooms_left > 0) {
			$this->buildOut($rooms_left, $depth);
			while($depth === 0 && $rooms_left > 0) {
				$this->buildOut($rooms_left, $depth);
			}
		}
	}

	public function buildOut(&$rooms_left, $depth)
	{
		$dirs = ['north', 'south', 'east', 'west', 'up', 'down'];
		shuffle($dirs);
		foreach($dirs as $dir) {
			if($rooms_left <= 0) {
				return;
			}
			if($this->$dir) {
				$r = Room::getByID($this->$dir);
				if($r instanceof self) {
					$r->buildOut($rooms_left, $depth);
				}
			} else if(chance() < 50) {
				$rooms_left--;
				$room = new static([Room::getReverseDirection($dir) => $this->id, 'title' => $this->title, 'description' => $this->description, 'area' => $this->area], $rooms_left, $depth+1);
				$this->$dir = $room->getID();
			}
		}
	}
}
?>
