<?php
namespace Phud\Items;

class Weapon extends Equipment
{
	const SWORD = 'sword';
	const AXE = 'axe';
	const MACE = 'mace';
	const STAFF = 'staff';
	const WHIP = 'whip';
	const DAGGER = 'dagger';
	const WAND = 'wand';
	const EXOTIC = 'exotic';
	const SPEAR = 'spear';
	const FLAIL = 'flail';
	
	protected $weapon_type = '';
	protected $verb = '';
	protected $damage_type = 0;
	
	public function __construct($properties = [])
	{
		$this->position = Equipment::POSITION_WIELD;
		parent::__construct($properties);
	}
	
	public function getWeaponType()
	{
		return $this->weapon_type;
	}
	
	public function getDamageType()
	{
		return $this->damage_type;
	}
	
	public function getVerb()
	{
		return $this->verb;
	}
	
	public function getInformation()
	{
		return
			"=====================\n".
			"==Weapon Attributes==\n".
			"=====================\n".
			"weapon type:         ".$this->getWeaponTypeLabel()."\n".
			"verb:                ".$this->getVerb()."\n".
			"damage type:         ".$this->damage_type."\n".
			parent::getInformation();
	}
}

?>
