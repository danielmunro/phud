<?php
namespace Mechanics;
class Area
{
	protected $fp = null;
	protected $last_added = null;
	protected $last_room = null;

	public function __construct($area)
	{
		$this->fp = fopen($area, 'r');
		while($line = $this->readLine()) {
			switch($line) {
				case strpos($line, 'room') === 0:
					$this->loadRoom(substr($line, strpos($line, ' ')+1));
					break 1;
				case strpos($line, 'item') === 0:
					$this->loadItems();
					break 1;
				case strpos($line, 'actor') === 0:
					$this->loadActors();
					break 1;
			}
		}
	}

	protected function loadRoom($id)
	{
		$getdir = function($d) {
			$directions = ['north', 'south', 'east', 'west', 'up', 'down'];
			foreach($directions as $dir) {
				if(strpos($dir, $d) === 0) {
					return $dir;
				}
			}
		};
		$p = $this->loadThing(['title', 'description' => 'block', 'area']);
		$p['id'] = $id;
		$line = $this->readLine();
		$break = false;
		while($line) {
			if(substr($line, -1) === '~') {
				$line = substr($line, 0, -1);
				$break = true;
			}
			list($dir, $id) = explode(' ', $line);
			$p[$getdir($dir)] = $id;
			if($break) {
				break;
			}
			$line = $this->readLine();
		}
		$this->last_added = $this->last_room = new Room($p);
	}

	protected function loadItems()
	{
		while($line = $this->readLine()) {
			if($line === "~") {
				break;
			}
			$p = $this->loadThing(['nouns', 'short', 'long' => 'block']);
			$class = ucfirst($line);
			$break = false;
			while($line = $this->readLine()) {
				if($line === "~") {
					break;
				}
				list($property, $value) = $this->parseProperty($line);
				if(substr($value, -1) === "~") {
					$value = substr($value, 0, -1);
					$break = true;
				}
				$p[$property] = is_integer($value) ? intval($value) : $value;
				if($break) {
					break;
				}
			}
			$full_class = 'Items\\'.$class;
			$this->last_added->addItem(new $full_class($p));
		}
	}

	protected function loadActors()
	{
		while($line = $this->readLine()) {
			if($line === '~') {
				break;
			}
			$p = $this->loadThing(['alias', 'nouns', 'long' => 'block', 'race']);
			$class = ucfirst($line);
			$full_class = 'Living\\'.$class;
			$this->last_added = new $full_class($p);
			$this->last_added->setRoom($this->last_room);
		}
	}

	protected function loadThing($properties)
	{
		foreach($properties as $property => $type) {
			$method = '';
			if(is_numeric($property)) {
				$property = $type;
				$type = 'line';
			}
			if($type === 'line') {
				$method = 'readLine';
			} else if($type === 'block') {
				$method = 'readBlock';
			} else if($type === 'property') {
				$method = 'readProperty';
			} else {
				Debug::log('Error in area parser: '.$type.' is not a defined type');
				continue;
			}
			$value = $this->$method();
			if(substr($value, -1) === '~') {
				$value = substr($value, 0, -1);
				$p[$property] = $value;
				return $p;
			}
			$p[$property] = $value;
		}
		return $p;
	}

	private function parseProperty($line)
	{
		return array_map(function($p) {
			$p = trim($p);
			var_dump($p);
			if($p === 'true') {
				return true;
			} else if($p === 'false') {
				return false;
			}
			return $p;
	 	}, explode(':', $line));
	}

	private function readLine()
	{
		$input = fgets($this->fp);
		$line = trim($input);
		if(strpos($line, '#') === 0 || (strlen($line) === 0 && $input !== false)) {
			return $this->readLine();
		}
		return $line;
	}

	private function readBlock()
	{
		$line = '';
		$block = '';
		$break = false;
		while($line = $this->readLine()) {
			if(substr($line, -1) === '~') {
				$line = substr($line, 0, -1);
				$break = true;
			}
			$block .= $line;
			if($break) {
				break;
			}
			$block .= "\n";
		}
		return $block;
	}
}
?>
