<?php
namespace Phud\Items;
use Phud\Inventory;

class Container extends Item
{
	use Inventory;

	public function getLong()
	{
		return parent::getLong()."\n".$this->displayContents();
	}
}
?>
