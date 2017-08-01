<?php

class curl {

	private $ch;

	public function initiate() {

		$this->ch = curl_init();
	}

	public function setOptArray($optArray) {

		curl_setopt_array($this->ch, $optArray);
	}

	public function execute() {

		$response = curl_exec($this->ch);

		return $response;
	}

	public function getErrorNumber() {

		return curl_errno($this->ch);
	}

	public function close() {

		curl_close($this->ch);
	}
}

?>
