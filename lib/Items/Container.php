<?php
namespace Phud\Items;
use Phud\Inventory,
	Phud\Usable;

class Container extends Item
{
	use Inventory, Usable;

	protected $short = 'a generic container';
	protected $long = 'A generic container lays here';
	private $inventory = null;

	public function getLong()
	{
		return parent::getLong()."\n".$this->displayContents();
	}
}
?>
