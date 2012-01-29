<?php
namespace Items;
use \Mechanics\Item;
class Food extends Item
{
	protected $short = 'a generic food item';
	protected $long = 'A generic food item lays here';
	protected $nouns = 'generic food';
	protected $nourishment = 1;

	public function getNourishment()
	{
		return $this->nourishment;
	}
	
	public function setNourishment($nourishment)
	{
		$this->nourishment = $nourishment;
	}
	
	public function getInformation()
	{
		return
			"===================\n".
			"==Food Attributes==\n".
			"===================\n".
			"nourishment:       ".$this->nourishment."\n".
			parent::getInformation();
	}
}

?>
