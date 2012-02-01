<?php
namespace Mechanics;
use \Items\Container;

trait Inventory
{
	protected $items = [];
	
	public function addItem(Item $item)
	{
		$this->items[] = $item;
	}
	
	public function removeItem(Item $item)
	{
		$i = array_search($item, $this->items);
		if($i !== false) {
			unset($this->items[$i]);
		}
		return $i;
	}
	
	public function getItems()
	{
		return $this->items;
	}

	public function getItemByInput($input)
	{
		return $this->getUsableByInput($this->items, $input);
	}
	
	public function getContainerByInput($input)
	{
		$container = $this->getUsableByInput($this->items, $input);
		return $container instanceof Container ? $container : null;
	}
	
	public function displayContents($show_prices = false)
	{
		$buffer = '';
		if(sizeof($this->items) > 0) {
			$items = [];
			$prices = [];
			foreach($this->items as $item) {
				if(!isset($items[$item->getShort()]))
					$items[$item->getShort()] = 0;
				$items[$item->getShort()] += 1;
				$prices[$item->getShort()] = $item->getValue();
			}
			foreach($items as $key => $item) {
				if($show_prices)
					$pre = $prices[$key] . ' copper - ';
				else
					$pre = ($item > 1 ? '(' . $item . ') ' : '' );
				$buffer .=  $pre . $key .  "\n";
			}
		} else {
			$buffer = "Nothing.";
		}
		return trim($buffer);
	}
	
	public function transferItemsFrom($inventory)
	{
		$items = $inventory->getItems();
		foreach($items as $item) {
			$inventory->removeItem($item);
			$this->addItem($item);
		}
	}
}
?>
