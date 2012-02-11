<?php
namespace Items;
use \Mechanics\Inventory,
	\Mechanics\Usable;

class Container extends Item
{
	use Inventory, Usable;

	protected $short = 'a generic container';
	protected $long = 'A generic container lays here';
	protected $nouns = 'generic container';
	private $inventory = null;

	public function getLong()
	{
		return parent::getLong()."\n".$this->displayContents();
	}
}
?>
