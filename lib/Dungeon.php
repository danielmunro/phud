<?php
namespace Phud;

class Dungeon extends Room
{
	public function __construct($properties = [], &$rooms_left = 0, $depth = 0, &$exit = null)
	{
		if(isset($properties['rooms'])) {
			$rooms_left = $properties['rooms'];
			unset($properties['rooms']);
		}
		if(isset($properties['exit'])) {
			$exit = $properties['exit'];
			unset($properties['exit']);
		}
		parent::__construct($properties);
		if($rooms_left > 0) {
			$this->buildOut($rooms_left, $depth, $exit);
			while($depth === 0 && $rooms_left > 0) {
				$this->buildOut($rooms_left, $depth, $exit);
			}
		}
	}

	public function buildOut(&$rooms_left, $depth, &$exit)
	{
		$dirs = ['north', 'south', 'east', 'west', 'up', 'down'];
		shuffle($dirs);
		foreach($dirs as $dir) {
			if($rooms_left <= 0) {
				return;
			}
			if($this->$dir) {
				$r = Room::getByID($this->$dir);
				if($r instanceof static) {
					$r->buildOut($rooms_left, $depth, $exit);
				}
			} else if(chance() < 50) {
				$rooms_left--;
				$room = new static([Room::getReverseDirection($dir) => $this->id, 'title' => $this->title, 'description' => $this->description, 'area' => $this->area], $rooms_left, $depth+1, $exit);
				$this->$dir = $room->getID();
				if($rooms_left === 0 && !is_null($exit)) {
					$rand_dir = rand(0, 5);
					while($room->$dirs[$rand_dir] > 0) {
						$rand_dir = rand(0, 5);
					}
					$exit_room = Room::getByID($exit);
					$room->setDirection($dirs[$rand_dir], $exit);
					$exit_room->setDirection(Room::getReverseDirection($dirs[$rand_dir]), $room->getID());
					$exit = null;
				}
			}
		}
	}
}
?>
