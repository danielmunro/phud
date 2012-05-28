<?php
namespace Phud;
use Phud\Abilities\Ability,
	Phud\Room\Area,
	Phud\Room\Room;

class Parser
{
	protected $fp = null;
	protected $last_added = null;
	protected $last_first_class = null;
	protected $last_room = null;
	protected $last_property = [];
	protected $break = false;
	protected $buffer = [];
	protected $area = null;
	protected static $aliases = [];
	protected static $defs = [];

	public function __construct($area_file)
	{
		$this->fp = fopen($area_file, 'r');
		while($line = $this->readLine()) {
			$method = $this->getMethod($line);
			$class = ucfirst($line);
			if($method && method_exists($this, $method)) {
				$this->$method($class);
			} else if(isset(self::$defs[$method])) {
				if(!is_callable(self::$defs[$method][1])) {
					Debug::log('Misconfigured area def: '.$method.'. Halting executing.');
					die;
				}
				call_user_func_array(self::$defs[$method][1], [$this, self::$defs[$method][0].'\\'.$class]);
			} else {
				Debug::log('Area method: "'.$method.'" does not exist.');
			}
		}
		if($this->area) {
			$this->area->setStatus('initialized');
		}
	}

	public function getArea()
	{
		return $this->area;
	}

	public function setArea(Area $area)
	{
		$this->area = $area;
	}

	public function getLastAdded()
	{
		return $this->last_added;
	}

	public function setLastAdded($last_added)
	{
		$this->last_added = $last_added;
	}

	public function getLastFirstClass()
	{
		return $this->last_first_class;
	}

	public function setLastFirstClass($last_first_class)
	{
		$this->last_added = $this->last_first_class = $last_first_class;
		$last_first_class->setArea($this->area);
	}
	
	public function getLastRoom()
	{
		return $this->last_room;
	}

	public function setLastRoom($last_room)
	{
		$this->last_room = $this->last_first_class = $this->last_added = $last_room;
		$last_room->setArea($this->area);
	}

	protected function def()
	{
		list($def, $full_class) = explode(' ', $this->readLine());
		$p = str_replace(['<?php', '?>'], '', $this->readBlock());
		$func = eval($p);
		self::$defs[$def] = [$full_class, $func];
	}

	protected function alias()
	{
		$aliases = explode(' ', $this->readLine());
		$object = array_pop($aliases);
		foreach($aliases as $alias) {
			self::$aliases[$alias] = $object;
		}
	}

	protected function getMethod($line)
	{
		if(isset(self::$aliases[$line])) {
			return self::$aliases[$line];
		}
		return $line;
	}

	public function loadRequired($properties, $additional = [])
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
			$value = $this->$types[$type](['comma' => 'accept']);
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
			if(strpos($value, '.') === false) {
				$value = intval($value);
			} else {
				$value = floatval($value);
			}
		}
		if($callback && $callback($p, $property, $value)) {
		} else {
			$p[$property] = $value;
		}
		$this->last_property = [$property, $value];
	}

	public function readLine($properties = [])
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
