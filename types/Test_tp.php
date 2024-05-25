<?php

namespace letter\types;

use letter\includes\interfaceType;

class Test_tp implements interfaceType {
	const SHOW_NAME = "Тестовый";
	const T_VERSION = 1;
	var $a = 0;

	public function setJs() {
		return;
	}

	public function constructSettingsPage() {
		$this->out = $this->mainControlPanel();
	}

	private function mainControlPanel() {
		$out = '<div id="mainCP"><form method="post" id=""><option><select value="1">1</select><select value="2">2</select></option></form></div>';

		return $out;
	}

	public function getHtml(): string {
		if ( isset( $this->out ) && ! empty( $this->out ) ) {
			return $this->out;
		} else {
			return '';
		}
	}

	public function ajaxHandler() {
		$a = 1;
	}
}