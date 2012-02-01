<?php
namespace Tests;
class Attributes extends \PHPUnit_Framework_TestCase
{
	private $attributes = null;
	
	public function setUp()
	{
		$this->attributes = new Attributes();
	}

	public function testAttributeChange()
	{
		$new_value = 1;
		$this->attributes->setStr($new_value);
	}
}
?>
