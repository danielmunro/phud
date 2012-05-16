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
		return property_exists($this, $key) ? $this->$key = $amount : false;
	}
}
?>
