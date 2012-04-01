<?php
namespace Phud\Items;

class Key extends Item
{
	protected $door_id = 0;

	public function getDoorID()
	{
		return $this->door_id;
	}
}
?>
