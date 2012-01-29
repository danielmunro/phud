<?php
namespace Items;
use \Mechanics\Item,
	\Mechanics\Server,
	\Mechanics\Actor;

class Drink extends Item
{
	protected $short = 'a generic drink container';
	protected $long = 'A generic drink container lays here';
	protected $nouns = 'generic drink container';
	protected $amount = 0;
	protected $contents = '';
	protected $thirst = 0;
	protected $uses = 0;
	
	public function getAmount()
	{
		return $this->amount;
	}
	
	public function setAmount($amount)
	{
		$this->amount = $amount;
		$this->uses = $amount;
	}
	
	public function drink(Actor $actor)
	{
		if($this->uses === 0)
		{
			Server::out($actor, "There's no ".$contents." left.");
			return false;
		}
		
		if($actor->increaseThirst($this->thirst)) {
			$this->uses--;
			return true;
		}
	}
	
	private function fill()
	{
		$this->uses = $this->amount;
	}
	
	public function getContents()
	{
		return $this->contents;
	}
	
	public function setContents($contents)
	{
		$this->contents = $contents;
		$this->fill();
	}

	public function setThirst($thirst)
	{
		$this->thirst = $thirst;
	}

	public function getThirst()
	{
		return $this->thirst;
	}
	
	public function getInformation()
	{
		return
			"==Drink Attributes==\n".
			"====================\n".
			"thirst:             ".$this->getThirst()."\n".
			"amount:             ".$this->getAmount()."\n".
			"contents:           ".$this->getContents()."\n".
			parent::getInformation();
	}
}

?>
