<?php
namespace Phud;

trait Identity
{
	protected $id = '';
	protected static $identities = [];

	public static function getByID($id)
	{
		if(isset(static::$identities[$id])) {
			return static::$identities[$id];
		}
	}

	public static function getAll()
	{
		return static::$identities;
	}

	public function getID()
	{
		return $this->id;
	}
}
?>
