<?php
namespace Mechanics;
class Area
{
	protected $fp = null;
	protected $last_added = null;
	protected $last_room = null;
	protected $break = false;
	protected $buffer = [];

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
		while($line = $this->readLine()) {
			list($dir, $id) = $this->parseProperty($line);
			$p[$getdir($dir)] = $id;
			if($this->_break()) {
				break;
			}
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
			while($line = $this->readLine()) {
				if($line === "~") {
					break;
				}
				list($property, $value) = $this->parseProperty($line);
				$p[$property] = is_integer($value) ? intval($value) : $value;
				if($this->_break()) {
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
			if($p === 'true') {
				return true;
			} else if($p === 'false') {
				return false;
			}
			return $p;
	 	}, preg_split('/\s/', trim($line), 2));
	}

	private function readLine($properties = [])
	{
		if($this->buffer) {
			$line = array_shift($this->buffer);
		} else {
			$input = fgets($this->fp);
			if($input === false) {
				return false;
			}
			$line = trim($input);
			$comment_pos = strpos($line, '#');
			if($comment_pos !== false) {
				$line = substr($line, 0, $comment_pos);
			}
			if(empty($line)) {
				return $this->readLine();
			}
			if((isset($properties['comma']) && $properties['comma'] !== 'accept') || !isset($properties['comma'])) {
				$comma_pos = strpos($line, ',');
				if($comma_pos !== false) {
					$this->buffer = explode(', ', $line);
					$line = array_shift($this->buffer);
				}
			}
		}
		if(substr($line, -1) === '~') {
			$this->break = true;
			$line = substr($line, 0, -1);
		}
		return $line;
	}

	private function readBlock()
	{
		$block = '';
		while($line = $this->readLine(['comma' => 'accept'])) {
			$block .= $line;
			if($this->_break()) {
				break;
			}
			$block .= "\n";
		}
		return $block;
	}

	private function _break()
	{
		if($this->break) {
			$this->break = false;
			return true;
		}
	}
}
?>
