<?php
namespace Phud\Room;
use Phud\EasyInit,
	Phud\Identity;

class Door
{
	use EasyInit, Identity;

	protected $short = 'a door';
	protected $long = 'a generic door is here.';
	protected $disposition = self::DISPOSITION_CLOSED;
	protected $default_disposition = self::DISPOSITION_CLOSED;
	protected $reload_ticks = 5;

	const DISPOSITION_LOCKED = 'locked';
	const DISPOSITION_OPEN = 'open';
	const DISPOSITION_CLOSED = 'closed';
	
	public function __construct($properties = [])
	{
		$this->initializeProperties($properties);
		self::$identities[$this->id] = $this;
	}

	public function decreaseReloadTick()
	{
		if($this->disposition != $this->default_disposition)
			$this->reload_ticks--;
		return $this->reload_ticks;
	}
	
	public function reload()
	{
		$this->disposition = $this->default_disposition;
		$this->is_hidden = $this->default_is_hidden;
	}
	
	public function getDisposition()
	{
		return $this->disposition;
	}
	
	public function setDisposition($disposition)
	{
		$this->disposition = $disposition;
	}
	
	public function getDefaultDisposition()
	{
		return $this->default_disposition;
	}
	
	public function setDefaultDisposition($default_disposition)
	{
		$this->default_disposition = $default_disposition;
	}
	
	public function getShort()
	{
		return $this->short;
	}
	
	public function getLong()
	{
		return $this->long;
	}
	
	public function __toString()
	{
		return $this->short;
	}
}
?>
