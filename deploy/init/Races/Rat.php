<?php
namespace Phud\Races;
use Phud\Attributes;

class Rat extends Race
{
	protected $alias = 'rat';

	protected function __construct()
	{
		$this->attributes = new Attributes();
		
		parent::__construct();
	}

	public function getSubscribers()
	{
		return [];
	}

	public function getAbilities()
	{
		return [];
	}
}
?>
