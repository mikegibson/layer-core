<?php

namespace Layer\Media\File;

interface FileInterface {

	public function getSize();

	public function getExtension();

	public function getMimeType();

	public function getHash();

	/**
	 * @return \DateTime
	 */
	public function getUpdated();

	public function getAbsolutePath();

}