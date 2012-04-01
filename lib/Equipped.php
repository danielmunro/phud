<?php
namespace Mechanics;
use \Items\Equipment;

class Equipped
{
	use Inventory, Usable;

	private $actor = null;
	private static $labels = [
		Equipment::POSITION_LIGHT =>		'<used as light>      ',
		Equipment::POSITION_FINGER =>	'<worn on finger>     ',
		Equipment::POSITION_NECK =>		'<worn around neck>   ',
		Equipment::POSITION_HEAD => 		'<worn on head>       ',
		Equipment::POSITION_LEGS => 		'<worn on legs>       ',
		Equipment::POSITION_FEET => 		'<worn on feet>       ',
		Equipment::POSITION_HANDS => 	'<worn on hands>      ',
		Equipment::POSITION_ARMS => 		'<worn on arms>       ',
		Equipment::POSITION_TORSO => 	'<worn on torso>      ',
		Equipment::POSITION_BODY => 		'<worn about body>    ',
		Equipment::POSITION_WAIST => 	'<worn about waist>   ',
		Equipment::POSITION_WRIST =>		'<worn around wrist>  ',
		Equipment::POSITION_WIELD => 	'<wielded in hand>    ',
		Equipment::POSITION_FLOAT => 	'<floating nearby>    '
	];
	private $equipment = [
		['position' => Equipment::POSITION_LIGHT, 'equipped' => null],
		['position' => Equipment::POSITION_FINGER, 'equipped' => null],
		['position' => Equipment::POSITION_FINGER, 'equipped' => null],
		['position' => Equipment::POSITION_NECK, 'equipped' => null],
		['position' => Equipment::POSITION_NECK, 'equipped' => null],
		['position' => Equipment::POSITION_BODY, 'equipped' => null],
		['position' => Equipment::POSITION_HEAD, 'equipped' => null],
		['position' => Equipment::POSITION_LEGS, 'equipped' => null],
		['position' => Equipment::POSITION_FEET, 'equipped' => null],
		['position' => Equipment::POSITION_HANDS, 'equipped' => null],
		['position' => Equipment::POSITION_ARMS, 'equipped' => null],
		['position' => Equipment::POSITION_TORSO, 'equipped' => null],
		['position' => Equipment::POSITION_WAIST, 'equipped' => null],
		['position' => Equipment::POSITION_WRIST, 'equipped' => null],
		['position' => Equipment::POSITION_WRIST, 'equipped' => null],
		['position' => Equipment::POSITION_WIELD, 'equipped' => null],
		['position' => Equipment::POSITION_WIELD, 'equipped' => null],
		['position' => Equipment::POSITION_FLOAT, 'equipped' => null],
	];
	
	public function __construct(Actor $actor)
	{
		$this->actor = $actor;
	}
	
	public static function getLabelByPosition($position)
	{
		return self::$labels[$position];
	}
	
	public function equip(Equipment $item, $display_message = true)
	{
		
		if($item->getPosition() === Equipment::POSITION_GENERIC) {
			if($display_message) {
				Server::out($this->actor, "You can't wear that.");
			}
			return false;
		}
		
		$positions = array_filter(
							$this->equipment,
							function($e) use ($item) { return $e['position'] === $item->getPosition(); }
						);
		
		$equipped = $dequipped = null;
		$i = 0;
		foreach($positions as $position)
		{
			$i++;
			$p = $position['position'];
			$e = $position['equipped'];
			if($e === null)
			{
				if($this->actor->removeItem($item) !== false) {
					$this->addItem($item);
				}
				$equipped = $item;
				$equipped_position = $p;
				break;
			}
			if($e !== null && $i == sizeof($positions))
			{
				$item_remove = $e;
				$this->removeItem($item_remove);
				$this->addItem($item);
				$this->actor->addItem($item_remove);
				$this->actor->removeItem($item);
				$equipped = $item;
				$dequipped = $item_remove;
				$equipped_position = $position;
				break;
			}
		}
		
		if($equipped)
		{
			foreach($this->equipment as &$e)
			{
				if($e['position'] === $equipped_position)
				{
					$e['equipped'] = $equipped;
					break;
				}
			}
		}
		
		if(!$display_message)
			return;
		
		if($dequipped)
		{
			$msg_you = "You remove ".$dequipped." and "; // . $equipped->getShort() . ' ' . $this->equipPositionLabel($actor, $equipped_position, true) . '.';
			$msg_others = ucfirst($this->actor)." removes ".$dequipped." and "; //wears " . $equipped->getShort() . ' ' . $this->equipPositionLabel($actor, $equipped_position) . '.';
		}
		else
		{
			$msg_you = "You ";
			$msg_others = ucfirst($this->actor)." ";
		}
		
		if($equipped->getPosition() === Equipment::POSITION_WIELD)
		{
			$msg_you .= 'wield ';
			$msg_others .= 'wields ';
		}
		else if($equipped->getPosition() == Equipment::POSITION_FLOAT)
		{
			$msg_you .= 'releases ';
			$msg_others .= 'releases ';
		}
		else if($equipped->getPosition() == Equipment::POSITION_HOLD)
		{
			$msg_you .= 'hold ';
			$msg_others .= 'holds ';
		}
		else
		{
			$msg_you .= 'wear ';
			$msg_others .= 'wears ';
		}
		
		$msg_you .= $item->getShort();
		$msg_others .= $item->getShort();
		
		$sex = $this->actor->getDisplaySex();
		
		switch($equipped->getPosition())
		{
			case Equipment::POSITION_LIGHT:
				$msg_you .= ' as a light.';
				$msg_others .= ' as a light.';
				break;
			case Equipment::POSITION_FLOAT:
				$msg_you .= ' to float around nearby.';
				$msg_others .= ' to float around nearby.';
				break;
			case Equipment::POSITION_WIELD:
				$msg_you .= '.';
				$msg_others .= '.';
				break;
			case Equipment::POSITION_FINGER:
				$msg_you .= ' on your finger.';
				$msg_others .= 'on ' . $sex . ' finger.';
				break;
			case Equipment::POSITION_ARMS:
				$msg_you .= ' on your arms.';
				$msg_others .= 'on ' . $sex . ' arms.';
				break;
			case Equipment::POSITION_BODY:
				$msg_you .= ' around your body.';
				$msg_others .= ' around ' . $sex . ' body.';
				break;
			case Equipment::POSITION_FEET:
				$msg_you .= ' on your feet.';
				$msg_others .= ' on ' . $sex . ' feet.';
				break;
			case Equipment::POSITION_HEAD:
				$msg_you .= ' on your head.';
				$msg_others .= ' on ' . $sex . ' head.';
				break;
			case Equipment::POSITION_HANDS:
				$msg_you .= ' on your hands.';
				$msg_others .= ' on ' . $sex . ' hands.';
				break;
			case Equipment::POSITION_HOLD:
				$msg_you .= ' in your hand.';
				$msg_others .= ' in ' . $sex . ' hand.';
				break;
			case Equipment::POSITION_TORSO:
				$msg_you .= ' around your torso.';
				$msg_others .= ' around ' . $sex . ' torso.';
				break;
			case Equipment::POSITION_WAIST:
				$msg_you .= ' around your waist.';
				$msg_others .= ' around ' . $sex . ' waist.';
				break;
			case Equipment::POSITION_WRIST:
				$msg_you .= ' on your wrist.';
				$msg_others .= ' on ' . $sex . ' wrist.';
				break;
		}
		
		Server::out($this->actor, $msg_you);
		$this->actor->getRoom()->announce($this->actor, $msg_others);
	}

	public function removeByPosition($position)
	{

		if($this->equipment[$position] instanceof Equipment)
		{
			$this->removeItem($item);
			$this->actor->removeAffects($item->getAffects());
			$item = $this->equipment[$position];
			$this->actor->addItem($item);
			$this->equipment[$position] = null;
		}
		else
			Server::out($this->actor, 'Nothing is there.');
		
	}
	
	public function remove(Equipment $item)
	{
		foreach($this->equipment as &$e)
		{
			if($e['equipped'] === $item)
			{
				$this->removeItem($item);
				$this->addItem($item);
				$e['equipped'] = null;
			}
		}
	}
	
	public function getEquipmentByPosition($position)
	{
		$eq = array_filter(
						$this->equipment,
						function($e) use ($position)
						{
							return $e['position'] === $position;
						}
					);
		if(sizeof($eq))
			return array_shift($eq);
		return null;
	}
	
	public function getEquipment()
	{
		return $this->equipment;
	}
	
	public function displayContents()
	{
	
		$buffer = '';
		$viewed = false;
		foreach($this->equipment as $eq)
		{
			$key = $eq['position'];
			$buf = self::$labels[$key];
			$len_diff = 22 - strlen($buf);
			for($i = 0; $i < $len_diff; $i++)
				$buf .= ' ';
			$buffer .= $buf;
			if($eq['equipped'] instanceof Equipment)
				$buffer .= '      ' . $eq['equipped']->getShort() . "\n";
			else
				$buffer .= "      nothing\n";
		}

		return $buffer;
	}
}
?>
