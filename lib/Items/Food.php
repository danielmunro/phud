<?php
namespace Phud\Items;

class Food extends Item
{
	protected $short = 'a generic food item';
	protected $long = 'A generic food item lays here';
	protected $nourishment = 1;

	public function getNourishment()
	{
		return $this->nourishment;
	}
	
	public function setNourishment($nourishment)
	{
		$this->nourishment = $nourishment;
	}
}
?>
