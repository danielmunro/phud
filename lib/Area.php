<?php
namespace Phud;

class Area
{
	use EasyInit;

	protected $alias = '';
	protected $terrain = '';
	protected $location = '';
	protected $status = 'new';

	public function __construct($initializing_properties)
	{
		$this->initializeProperties($initializing_properties);
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function __toString()
	{
		return $this->alias;
	}
}
?>
