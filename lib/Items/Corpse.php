<?php
namespace Phud\Items;
class Corpse extends Container
{

	protected $gold = 0;
	protected $copper = 0;
	protected $silver = 0;
	
	public function getGold()
	{
		return $this->gold;
	}
	
	public function setGold($gold)
	{
		$this->gold = $gold;
	}
	
	public function addGold($gold)
	{
		$this->gold += $gold;
	}
	
	public function getSilver()
	{
		return $this->silver;
	}
	
	public function setSilver($silver)
	{
		$this->silver = $silver;
	}
	
	public function addSilver($silver)
	{
		$this->silver += $silver;
	}
	
	public function getCopper()
	{
		return $this->copper;
	}
	
	public function setCopper($copper)
	{
		$this->copper = $copper;
	}
	
	public function addCopper($copper)
	{
		$this->copper += $copper;
	}
}
?>
