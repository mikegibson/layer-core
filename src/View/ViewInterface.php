<?php

namespace Layer\View;

interface ViewInterface {

	public function render($template, array $data = []);

}