<?php
namespace Mechanics;
class Area
{
	protected $fp = null;

	public function __construct($area)
	{
		$this->fp = fopen($area, 'r');
		while($line = $this->readLine()) {
			switch($line) {
				case 'rooms':
					$this->loadRooms();
					break;
			}
		}
	}

	protected function loadRooms()
	{
		$getdir = function($d) {
			$directions = ['north', 'south', 'east', 'west', 'up', 'down'];
			foreach($directions as $dir) {
				if(strpos($dir, $d) === 0) {
					return $dir;
				}
			}
		};
		while($line = $this->readLine()) {
			$p = [];
			$p['id'] = $line;
			$p['title'] = $this->readLine();
			$p['description'] = $this->readBlock();
			$p['area'] = $this->readLine();
			$line = $this->readLine();
			while($line != "~" && $line) {
				list($dir, $id) = explode(' ', $line);
				$p[$getdir($dir)] = $id;
				$line = $this->readLine();
			}
			new Room($p);
		}
	}

	private function readLine()
	{
		$line = fgets($this->fp);
		if(strpos($line, '#') === 0) {
			return $this->readLine();
		}
		return trim($line);
	}

	private function readBlock()
	{
		$line = '';
		$block = '';
		while($line != "~") {
			$line = $this->readLine();
			$block .= $line;
		}
		return $block;
	}
}
?>
