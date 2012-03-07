<?php
namespace Mechanics;
use \Mechanics\Affect,
	\Mechanics\Ability\Ability,
	\Living\Mob;

class Area
{
	protected $fp = null;
	protected $last_added = null;
	protected $last_first_class = null;
	protected $last_room = null;
	protected $last_property = [];
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
				case 'equipment':
				case 'container':
				case 'furniture':
				case 'key':
					$this->loadItem(ucfirst($line));
					break 1;
				case 'mob':
					$this->loadMob();
					break 1;
				case 'shopkeeper':
					$this->loadShopkeeper();
					break 1;
				case 'glow':
				case 'poison':
					$this->loadAffect(ucfirst($line));
					break 1;
				case 'door':
					$this->loadDoor();
			}
		}
	}

	protected function loadDoor()
	{
		$p = $this->loadRequired(['short', 'long'], ['properties']);
		$this->last_added = new Door($p);
	}

	protected function loadAffect($affect)
	{
		$affect = 'Affects\\'.$affect;
		$p = $this->loadRequired([], ['properties']);
		$this->last_added->addAffect(new $affect($p));
	}

	protected function loadRoom()
	{
		$p = $this->loadRequired(
			['title', 'description' => 'block', 'area'], 
			['properties' => function(&$p, $property, $value) {
				$long = ['north', 'south', 'east', 'west', 'up', 'down'];
				foreach($long as $l) {
					if(strpos($l, $property) === 0) {
						$p[$l] = $value;
						return true;
					}
				}
		}]);
		$this->last_added = $this->last_first_class = $this->last_room = new Room($p);
	}

	protected function loadItem($class)
	{
		$p = $this->loadRequired(['short', 'long' => 'block'], ['properties', 'attributes']);
		$class = 'Items\\'.$class;
		$this->last_added = new $class($p);
		$this->last_first_class->addItem($this->last_added);
		if($this->last_first_class instanceof Mob) {
			$this->last_first_class->setRepopItemProperties();
		}
	}

	protected function loadMob()
	{
		$this->loadActor('Mob', [], ['properties', 'attributes', 'abilities']);
		if(!$this->last_added->getArea()) {
			// Game rule: every mob needs a default area. If one is not assigned, set
			// it to the area where they first pop.
			$this->last_added->setArea($this->last_room->getArea());
		}
	}

	protected function loadShopkeeper()
	{
		$this->loadActor('Shopkeeper', [], ['properties']);
		$this->last_added->addAbility(Ability::lookup('haggle')); // All shopkeepers get haggle. It's part of the trade
	}

	protected function loadActor($class, $required_properties = [], $additional = [])
	{
		if(empty($required_properties)) {
			$required_properties = ['alias', 'long' => 'block'];
		}
		if(empty($additional)) {
			$additional = ['properties', 'attributes'];
		}
		$p = $this->loadRequired($required_properties, $additional);
		$class = 'Living\\'.$class;
		$this->last_added = $this->last_first_class = new $class($p);
		$this->last_added->setRoom($this->last_room);
	}

	protected function loadRequired($properties, $additional = [])
	{
		$types = ['line' => 'readLine', 'block' => 'readBlock'];
		$p = [];
		foreach($properties as $property => $type) {
			$method = '';
			if(is_numeric($property)) {
				$property = $type;
				$type = 'line';
			}
			if(!isset($types[$type])) {
				Debug::log('Error in area parser: '.$type.' is not a defined type');
				continue;
			}
			$value = $this->$types[$type]();
			if(substr($value, -1) === '~') {
				$value = substr($value, 0, -1);
				$p[$property] = $value;
				return $p;
			}
			$p[$property] = $value;
		}
		foreach($additional as $key => $value) {
			if(is_numeric($key)) {
				$add = $value;
				$callback = null;
			} else {
				$add = $key;
				$callback = $value;
			}
			if($add === 'properties') {
				$this->_parseProperties($p, $callback);
			}
			else if($add === 'attributes') {
				$this->_parseAttributes($p);
			}
			else if($add === 'abilities') {
				$this->_parseAbilities($p);
			}
		}
		return $p;
	}

	private function _parseAttributes(&$p)
	{
		$p['attributes'] = [];
		while($line = $this->readLine()) {
			$this->parseInto($p, $line, function(&$p, $property, $value) {
				$p['attributes'][$property] = $value;
				return true;
			});
		}
	}

	private function _parseProperties(&$p, $callback = null)
	{
		while($line = $this->readLine()) {
			$this->parseInto($p, $line, $callback);
		}
	}

	private function _parseAbilities(&$p)
	{
		$p['abilities'] = [];
		while($line = $this->readLine()) {
			$ability = Ability::lookup($line);
			if($ability) {
				$p['abilities'][] = $ability;
			} else {
				Debug::log('Ability does not exist: '.$line);
			}
		}
	}

	private function parseInto(&$p, $line, $callback = null)
	{
		$x = preg_split('/\s/', trim($line), 2);
		if(!isset($x[1])) {
			Debug::log('Error in parser. Expecting key-value pair, got: '.print_r($x, true));
			echo "\n\nError in parser. Expecting key-value pair, got: \n\n";
			var_dump($x);
			echo "\n\n";
			echo "Currently parsing: \n\n";
			var_dump($p);
			die;
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
		} else {
			$p[$property] = $value;
		}
		$this->last_property = [$property, $value];
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
