<?php
namespace Phud;

class Attributes
{
	protected $str = 0;
	protected $int = 0;
	protected $wis = 0;
	protected $dex = 0;
	protected $con = 0;
	protected $cha = 0;
	
	protected $hp = 0;
	protected $mana = 0;
	protected $movement = 0;
	
	protected $ac_bash = 0;
	protected $ac_slash = 0;
	protected $ac_pierce = 0;
	protected $ac_magic = 0;
	
	protected $hit = 0;
	protected $dam = 0;
	
	protected $saves = 0;

	public function __construct($attributes = [])
	{
		foreach($attributes as $key => $value) {
			$this->modifyAttribute($key, $value);
		}
	}

	public function getAttribute($key)
	{
		return $this->$key;
	}

	public function modifyAttribute($key, $amount)
	{
		$this->$key += $amount;
	}
	
	public function setAttribute($key, $amount)
	{
		if(property_exists($this, $key)) {
			$this->$key = $amount;
			return true;
		}
		return false;
	}
	
	public function getAttributeLabels()
	{
		$msg = '';
		if($this->str)
			$msg .= 'Affects str by '.$this->str."\n";
		if($this->int)
			$msg .= 'Affects int by '.$this->int."\n";
		if($this->wis)
			$msg .= 'Affects wis by '.$this->wis."\n";
		if($this->dex)
			$msg .= 'Affects dex by '.$this->dex."\n";
		if($this->con)
			$msg .= 'Affects con by '.$this->con."\n";
		if($this->cha)
			$msg .= 'Affects cha by '.$this->cha."\n";
		if($this->hp)
			$msg .= 'Affects hp by '.$this->hp."\n";
		if($this->mana)
			$msg .= 'Affects mana by '.$this->mana."\n";
		if($this->movement)
			$msg .= 'Affects movements by '.$this->movement."\n";
		if($this->ac_bash)
			$msg .= 'Affects bash ac by '.$this->ac_bash."\n";
		if($this->ac_slash)
			$msg .= 'Affects slash ac by '.$this->ac_slash."\n";
		if($this->ac_pierce)
			$msg .= 'Affects pierce ac by '.$this->ac_pierce."\n";
		if($this->ac_magic)
			$msg .= 'Affects magic ac by '.$this->ac_magic."\n";
		if($this->hit)
			$msg .= 'Affects hit roll by '.$this->hit."\n";
		if($this->dam)
			$msg .= 'Affects dam roll by '.$this->dam."\n";
		if($this->saves)
			$msg .= 'Affects saves by '.$this->saves."\n";
		return $msg;
	}
}
?>
