<?php
namespace Phud\Items;
use Phud\Equipped;

class Equipment extends Item
{
	const POSITION_LIGHT = 'light';
	const POSITION_FINGER = 'finger';
	const POSITION_NECK = 'neck';
	const POSITION_BODY = 'body';
	const POSITION_HEAD = 'head';
	const POSITION_LEGS = 'legs';
	const POSITION_FEET = 'feet';
	const POSITION_HANDS = 'hands';
	const POSITION_ARMS = 'arms';
	const POSITION_TORSO = 'torso';
	const POSITION_WAIST = 'waist';
	const POSITION_WRIST = 'wrist';
	const POSITION_HOLD = 'hold';
	const POSITION_FLOAT = 'float';
	const POSITION_WIELD = 'wield';
	const POSITION_GENERIC = 'generic';

	protected $position = 0;
	protected $condition = 100;
	protected $size = 0;
	
	public function getPosition()
	{
		return $this->position;
	}
	
	public function getCondition()
	{
		return $this->condition;
	}
	
	public function modifyCondition($condition)
	{
		$this->condition += $condition;
	}
	
	public function getSize()
	{
		return $this->size;
	}
	
	public static function getPositionByStr($position)
	{
		switch(strtolower($position))
		{
			case strpos('light', $position) === 0:
				return self::POSITION_LIGHT;
			case strpos('finger', $position) === 0:
				return self::POSITION_FINGER;
			case strpos('neck', $position) === 0:
				return self::POSITION_NECK;
			case strpos('body', $position) === 0:
				return self::POSITION_BODY;
			case strpos('head', $position) === 0:
				return self::POSITION_HEAD;
			case strpos('legs', $position) === 0:
				return self::POSITION_LEGS;
			case strpos('feet', $position) === 0:
				return self::POSITION_FEET;
			case strpos('hands', $position) === 0:
				return self::POSITION_HANDS;
			case strpos('arms', $position) === 0:
				return self::POSITION_ARMS;
			case strpos('torso', $position) === 0:
				return self::POSITION_TORSO;
			case strpos('waist', $position) === 0:
				return self::POSITION_WAIST;
			case strpos('wrist', $position) === 0:
				return self::POSITION_WRIST;
			case strpos('hold', $position) === 0:
				return self::POSITION_HOLD;
			case strpos('float', $position) === 0:
				return self::POSITION_FLOAT;
			case strpos('wield', $position) === 0:
				return self::POSITION_WIELD;
			default:
				return false;
		}
	}
	
	public function getInformation()
	{
		return 
			"===========================\n".
			"== Equipment Information ==\n".
			"===========================\n".
			"position:              ".Equipped::getLabelByPosition($this->position)."\n".
			"condition:             ".$this->getCondition()."\n".
			"size:                  ".$this->getSize().
			parent::getInformation();
	}
}

?>
