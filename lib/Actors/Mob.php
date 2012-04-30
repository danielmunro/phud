<?php
namespace Phud\Actors;
use Phud\Dbr,
	Phud\Room,
	Phud\Debug,
	Phud\Server,
	Phud\Command\Command,
	Phud\Nouns,
	Phud\Items\Corpse,
	Phud\Items\Item;

class Mob extends Actor
{
	use Nouns;

	protected $movement = 0;
	protected $movement_timeout = 0;
	protected $respawn_ticks = 1;
	protected $respawn_ticks_timeout = 1;
	protected $auto_flee = false;
	protected $unique = false;
	protected $default_respawn_ticks = 1;
	protected $start_room_id = 0;
	protected $area = '';
	protected $gold_repop = 0;
	protected $silver_repop = 0;
	protected $copper_repop = 0;
	protected $alias = 'a generic mob';
	protected $path = [];
	protected $is_recording_path = false;
	protected $path_index = -1;
	protected $last_path_index = -2;
	protected $repop_item_properties = [];
	
	const FLEE_PERCENT = 10;
	
	public function __construct($properties = [])
	{
		parent::__construct($properties);
		if($this->movement) {
			$this->movement_timeout = $this->movement;
			$this->on(
				'tick',
				function($event, $mob) {
					$mob->evaluateMove();
				}
			);
		}
	}
	
	public static function runInstantiation()
	{
		$db = Dbr::instance();
		$mob_ids = $db->sMembers('mobs');
		foreach($mob_ids as $mob_id) {
			unserialize($db->get($mob_id));
		}
	}

	public function addItem(Item $item)
	{
		if($this->area->getStatus() === 'new') {
			$this->repop_item_properties[] = $item->getInitializingProperties();
			$repop = isset($item->getInitializingProperties()['repop']) ? $item->getInitializingProperties()['repop'] : 100;
			if($repop > chance()) {
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

	public function evaluateMove()
	{
		$this->movement_timeout--;
		if($this->movement_timeout < 0) {
			$min = $this->movement * 0.5;
			$max = $this->movement * 2;
			$this->movement_timeout = round(rand($min, $max));
			$this->move();
		}
	}
	
	public function move()
	{
		$r = $this->getRoom();
		if($r->getID() === Room::PURGATORY_ROOM_ID)
		{
			return;
		}

		$directions = [];
		foreach(Room::getDirections() as $direction) {
			$directions[$direction] = $r->getDirection($direction);
		}

		if($this->path) {
			if($this->path_index > $this->last_path_index) {
				$this->path_index++;
				$this->last_path_index++;
				if($this->path_index > sizeof($this->path)-1) {
					$this->path_index = sizeof($this->path)-1;
					$this->last_path_index = sizeof($this->path);
					$direction = Room::getReverseDirection($this->path[$this->path_index]);
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
					$direction = Room::getReverseDirection($this->path[$this->path_index]);
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
				$command = Command::lookup($dir);
				$command['lookup']->perform($this);
				return;
			}
		}
	}
	
	public function handleDeath()
	{
		parent::handleDeath();
		$this->setAttribute('hp', -1);
		$this->setRoom(Room::find(Room::PURGATORY_ROOM_ID));
		$this->respawn_ticks_timeout = round(rand($this->respawn_ticks - ($this->respawn_ticks / 2), $this->respawn_ticks + ($this->respawn_ticks / 2)));
		$this->on(
			'tick',
			function($event, $server, $mob) {
				$mob->evaluateRespawn();
				if($mob->isAlive()) {
					$event->kill();
				}
			}
		);
	}

	public function evaluateRespawn()
	{
		$this->respawn_ticks_timeout--;
		if($this->respawn_ticks_timeout < 0) {
			$this->setAttribute('hp', $this->getMaxAttribute('hp'));
			$this->setAttribute('mana', $this->getMaxAttribute('mana'));
			$this->setAttribute('movement', $this->getMaxAttribute('movement'));
			$this->setRoom($this->getStartRoom());
			$this->getRoom()->announce([
				['actor' => '*', 'message' => ucfirst($this).' arrives in a puff of smoke.']
			]);
			foreach($this->repop_item_properties as $p) {
				if($p['repop'] > chance()) {
					$this->addItem(new Item($p));
				}
			}
		}
	}
	
	public function getExperiencePerLevel()
	{
		return $this->getExperiencePerLevelFromCP();
	}
	
	public function getStartRoom()
	{
		return Room::find($this->start_room_id);
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
	
	public static function validateAlias($alias)
	{
		return preg_match('/^[A-Za-z ]{2,100}$/i', $alias);
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

	protected function getDefaultNouns()
	{
		return $this->alias;
	}
}
?>
