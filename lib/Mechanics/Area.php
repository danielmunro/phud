<?php
namespace Mechanics;
use \Mechanics\Affect,
	\Mechanics\Ability\Ability;

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
				case 'room':
					$this->loadRoom();
					break 1;
				case 'item':
				case 'drink':
				case 'food':
				case 'armor':
				case 'equipment':
				case 'container':
					$this->loadItem(ucfirst($line));
					break 1;
				case 'mob':
					$this->loadMob();
					break 1;
				case 'shopkeeper':
					$this->loadActor('shopkeeper');
					break 1;
				case 'affect':
					$this->loadAffect();
					break 1;
			}
		}
	}

	protected function loadAffect()
	{
		$p = [];
		while($line = $this->readLine()) {
			$this->parseInto($p, $line);
		}
		$this->last_added->addAffect(new Affect($p));
	}

	protected function loadRoom()
	{
		$p = $this->loadRequired(['title', 'description' => 'block', 'area']);
		while($line = $this->readLine()) {
			$this->parseInto($p, $line, function(&$p, $property, $value) {
				$long = ['north', 'south', 'east', 'west', 'up', 'down'];
				foreach($long as $l) {
					if(strpos($l, $property) === 0) {
						$p[$l] = $value;
						return true;
					}
				}
			});
		}
		$this->last_added = $this->last_room = new Room($p);
	}

	protected function loadItem($class)
	{
		$p = $this->loadRequired(['short', 'long' => 'block']);
		while($line = $this->readLine()) {
			$this->parseInto($p, $line);
		}
		$this->parseAttributes($p);
		$full_class = 'Items\\'.$class;
		$this->last_added->addItem(new $full_class($p));
	}

	protected function loadMob()
	{
		$this->loadActor('Mob');
		if(!$this->last_added->getArea()) {
			$this->last_added->setArea($this->last_room->getArea());
		}
		while($line = $this->readLine()) {
			$ability = Ability::lookup($line);
			if($ability) {
				$this->last_added->addAbility($ability);
			} else {
				Debug::log('Ability does not exist: '.$line);
			}
		}
	}

	protected function loadActor($class)
	{
		$p = $this->loadRequired(['alias', 'long' => 'block']);
		while($line = $this->readLine()) {
			$this->parseInto($p, $line);
		}
		$this->parseAttributes($p);
		$full_class = 'Living\\'.$class;
		$this->last_added = new $full_class($p);
		$this->last_added->setRoom($this->last_room);
	}

	protected function loadRequired($properties)
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

	private function parseAttributes(&$p)
	{
		$p['attributes'] = [];
		while($line = $this->readLine()) {
			$this->parseInto($p, $line, function(&$p, $property, $value) {
				$p['attributes'][$property] = $value;
				return true;
			});
		}
	}

	private function parseInto(&$p, $line, $callback = null)
	{
		$x = preg_split('/\s/', trim($line), 2);
		if(!isset($x[1])) {
			var_dump($x);die;
		}
		list($property, $value) = $x;
		$value = trim($value);
		if($value === 'true') {
			$value = true;
		} else if($value === 'false') {
			$value = false;
		} else if(is_numeric($value)) {
			$value = intval($value);
		}
		if($callback && $callback($p, $property, $value)) {
			return;
		}
		$p[$property] = $value;
	}

	private function readLine($properties = [])
	{
		if($this->_break()) {
			return false;
		}
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
		if($line === '~') {
			return false;
		}
		if(substr($line, -1) === '~') {
			$this->break = true;
			$line = substr($line, 0, -1);
		}
		return $line;
	}

	private function readBlock()
	{
		$this->break = false;
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
