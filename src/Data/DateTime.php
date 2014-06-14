<?php

namespace Layer\Data;

class DateTime extends \DateTime {

	protected $toStringFormat = 'Y-m-d H:i:s';

	public function __toString() {
		return $this->format($this->toStringFormat);
	}

}