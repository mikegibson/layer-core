<?php

namespace Sentient\View;

interface ViewInterface {

	public function render($template, array $data = []);

}