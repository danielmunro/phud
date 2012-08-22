<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor;

class Item extends Argument
{
	protected $search_in = null;

	public function __construct($search_in)
	{
		$this->search_in = $search_in;
	}

	public function parse($arg)
	{
		if($this->search_in && $item = $this->search_in->getItemByInput($arg)) {
			return $item;
		}
		$this->failItem();
	}

	protected function failItem()
	{
		$this->fail("Nothing is there.");
	}
}
