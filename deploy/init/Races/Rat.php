<?php
namespace Races;
use Phud\Race,
	Phud\Attributes;

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
