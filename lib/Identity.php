<?php
namespace Phud;

trait Identity
{
	protected $id = '';
	protected static $identities = [];

	public static function getByID($id)
	{
		if(isset(static::$identities[$id]) && static::$identities[$id] instanceof static) {
			return static::$identities[$id];
		}
	}

	public function getID()
	{
		return $this->id;
	}
}
?>
