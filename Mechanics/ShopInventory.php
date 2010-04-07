<?php

	class ShopInventory extends Inventory
	{
	
		public function __construct($id)
		{
			$this->id = $id;
			$this->table = 'shop';
		}
		
		public function displayContents()
		{
			$buffer = '';
			if(is_array($this->items))
				foreach($this->items as $item)
					$buffer .= $item->getValue() . ' copper - ' . ucfirst($item->getShort());
			else
				$buffer = "Nothing.";
			
			return $buffer;
		}
	
	}

?>
