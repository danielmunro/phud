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
			while($depth === 0 && $rooms_left > 0) {
				$this->buildOut($rooms_left, $depth, $exit);
			}
		} else if(!is_null($exit)) {
			$dirs = ['north', 'south', 'east', 'west', 'up', 'down'];
			$rand_dir = rand(0, 5);
			while($this->$dirs[$rand_dir] > 0) {
				$rand_dir = rand(0, 5);
			}
			$exit_room = Room::getByID($exit);
			$this->$dirs[$rand_dir] = $exit;
			$exit_room->setDirection(Room::getReverseDirection($dirs[$rand_dir]), $this->id);
			$exit = null;
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
			$r = null;
			if($this->$dir) {
				$r = Room::getByID($this->$dir);
			} else if(chance() < 50) {
				$rooms_left--;
				$r = new static([Room::getReverseDirection($dir) => $this->id, 'title' => $this->title, 'description' => $this->description, 'area' => $this->area], $rooms_left, $depth+1, $exit);
				$this->$dir = $r->getID();
			}
			if($r instanceof static) {
				$r->buildOut($rooms_left, $depth, $exit);
			}
		}
	}
}
?>
