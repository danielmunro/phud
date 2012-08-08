<?php
namespace Phud\Actors;
use Phud\Dbr,
	Phud\Room\Room,
	Phud\Room\Direction,
	Phud\Debug,
	Phud\Server,
	Phud\Commands\Command,
	Phud\Items\Corpse,
	Phud\Items\Item;

class Mob extends Actor
{
	protected $movement = 0;
	protected $respawn_ticks = 3;
	protected $auto_flee = false;
	protected $unique = false;
	protected $start_room_id = 0;
	protected $area = null;
	protected $gold_repop = 0;
	protected $silver_repop = 0;
	protected $copper_repop = 0;
	protected $path = [];
	protected $is_recording_path = false;
	protected $path_index = -1;
	protected $last_path_index = -2;
	protected $repop_item_properties = [];
	protected static $counter = 0;
	
	const FLEE_PERCENT = 10;
	
	public function __construct($properties = [])
	{
		parent::__construct($properties);
		self::$counter++;
	}

	public static function getCounter()
	{
		return self::$counter;
	}

	public function applyListeners()
	{
		parent::applyListeners();
		$this->on('died', function($event, $mob) {
			$mob->setRoom(Room::getByID(Room::PURGATORY_ROOM_ID));
			$t = $mob->getRespawnTicks();
			$respawn_timeout = round(rand($t - ($t / 2), $t + ($t / 2)));
			$mob->on('tick', function($event, $mob) use (&$respawn_timeout) {
				$respawn_timeout--;
				if($respawn_timeout < 1) {
					$mob->respawn();
					$event->kill();
				}
			});
		});
		if($this->movement) {
			$timeout = $this->movement;
			$this->on(
				'tick',
				function($event, $mob) use (&$timeout) {
					$timeout--;
					if($timeout < 0) {
						$min = $this->movement * 0.5;
						$max = $this->movement * 2;
						$timeout = max(1, round(rand($min, $max)));
						$this->move();
					}
				}
			);
		}
	}

	public function addItem(Item $item)
	{
		if($this->area->getStatus() === 'new') {
			$this->repop_item_properties[] = [get_class($item), $item->getInitializingProperties()];
			if($item->getRepop() > chance()) {
				parent::addItem($item);
			}
		} else {
			parent::addItem($item);
		}
	}

	public function setRoom(Room $room)
	{
		parent::setRoom($room);
		if(empty($this->start_room_id)) {
			$this->start_room_id = $this->room->getID();
		}
	}

	public function getPath()
	{
		return $this->path;
	}

	public function isRecordingPath($toggle = null)
	{
		if($toggle === null) {
			return $this->is_recording_path;
		} else if(is_bool($toggle)) {
			$this->is_recording_path = $toggle;
		}
	}

	public function addPath($input)
	{
		$this->path[] = $input;
	}

	public function resetPath()
	{
		$this->path = [];
	}

	public function move()
	{
		if(!$this->is_alive) {
			return;
		}

		$r = $this->getRoom();

		$directions = [];
		foreach(Direction::getDirections() as $direction) {
			$directions[$direction] = $r->getDirection($direction);
		}

		if($this->path) {
			if($this->path_index > $this->last_path_index) {
				$this->path_index++;
				$this->last_path_index++;
				if($this->path_index > sizeof($this->path)-1) {
					$this->path_index = sizeof($this->path)-1;
					$this->last_path_index = sizeof($this->path);
					$direction = Direction::getReverse($this->path[$this->path_index]);
				} else {
					$direction = $this->path[$this->path_index];
				}
			} else {
				$this->path_index--;
				$this->last_path_index--;
				if($this->path_index < 0) {
					$this->path_index = 0;
					$this->last_path_index = -1;
					$direction = $this->path[$this->path_index];
				} else {
					$direction = Direction::getReverse($this->path[$this->path_index]);
				}
			}
			Debug::log($this.' is moving, path index: '.$this->path_index.', direction: '.$direction);
			foreach($directions as $alias => $d) {
				if(strpos($alias, $direction) === 0) {
					$directions = [$direction => $d];
				}
			}
		} else {
			$direction = rand(0, sizeof($directions)-1);
			$directions = array_filter(
									$directions,
									function($d)
									{
										return $d instanceof Room;
									}
								);
			uasort(
				$directions,
				function($i)
				{
					return rand(0, 1);
				}
			);
		}
		$areas = explode(' ', $this->area);
		foreach($directions as $dir => $room)
		{
			$other_areas = explode(' ', $room->getArea());
			$intersection = array_intersect($areas, $other_areas);
			if($intersection)
			{
				Command::lookup($dir)->perform($this, $dir);
				return;
			}
		}
	}

	public function respawn()
	{
		parent::respawn();
		$this->setAttribute('hp', $this->getMaxAttribute('hp'));
		$this->setAttribute('mana', $this->getMaxAttribute('mana'));
		$this->setAttribute('movement', $this->getMaxAttribute('movement'));
		$this->setRoom($this->getStartRoom());
		$this->getRoom()->announce([
			['actor' => $this, 'message' => 'You arrive in a puff of smoke.'],
			['actor' => '*', 'message' => ucfirst($this).' arrives in a puff of smoke.']
		]);
		foreach($this->repop_item_properties as $p) {
			$class = $p[0];
			$properties = $p[1];
			$percent = isset($properties['repop']) ? $properties['repop'] : 1;
			if($percent > chance()) {
				$this->addItem(new $class($properties));
			}
		}
	}
	
	public function getStartRoom()
	{
		return Room::getByID($this->start_room_id);
	}
	
	public function setStartRoom()
	{
		$this->start_room_id = $this->room->getID();
	}
	
	public function getMovement()
	{
		return $this->movement;
	}

	public function setMovement($ticks)
	{
		$this->movement = intval($ticks);
	}

	public function getRespawnTicks()
	{
		return $this->respawn_ticks;
	}

	public function getAutoFlee()
	{
		return $this->auto_flee;
	}
	
	public function setAutoFlee($auto_flee)
	{
		$this->auto_flee = $auto_flee;
	}

	public function isUnique()
	{
		return $this->unique;
	}
	
	public function setUnique($unique)
	{
		$this->unique = $unique;
	}
	
	public function getArea()
	{
		return $this->area;
	}
	
	public function setArea($area)
	{
		$this->area = $area;
	}

	public function notify($message)
	{
	}
	
	///////////////////////////////////////////////////////////////////////////
	// Money stuff
	///////////////////////////////////////////////////////////////////////////
	
	public function getGoldRepop()
	{
		return $this->gold_repop;
	}
	
	public function setGoldRepop($gold)
	{
		$this->gold_repop = $gold;
	}
	
	public function getSilverRepop()
	{
		return $this->silver_repop;
	}
	
	public function setSilverRepop($silver)
	{
		$this->silver_repop = $silver;
	}
	
	public function getCopperRepop()
	{
		return $this->copper_repop;
	}
	
	public function setCopperRepop($copper)
	{
		$this->copper_repop = $copper;
	}
}
?>
