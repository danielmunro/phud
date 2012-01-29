<?php
namespace Mechanics;

class Anonymous
{
	public function __construct()
	{
	}

	public function _require_once($file)
	{
		require_once($file);
	}
}
?>
