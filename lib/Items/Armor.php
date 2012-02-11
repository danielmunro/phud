<?php
namespace Items;

class Armor extends Equipment
{
	protected $short = 'a generic piece of armor';
	protected $long = 'A generic piece of armor lays here';
	protected $nouns = 'generic armor';
	
	public function __construct($properties = [])
	{
		$this->position = Equipment::POSITION_GENERIC;
		parent::__construct($properties);
	}
}
?>
