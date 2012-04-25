<?php
namespace Phud;

class Area
{
	protected $alias = '';
	protected $status = 'new';

	public function __construct()
	{
	}

	public function parse($area_file)
	{
		new Parser($area_file);
		$this->status = 'initialized';
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
