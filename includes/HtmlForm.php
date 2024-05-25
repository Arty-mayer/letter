<?php

namespace letter\includes;

class HtmlForm {
	public bool $configIsSet;
	private array $config;
	private array $classes;
	private string $htmlForm;

	function __construct( $set = null ) {
		$this->configIsSet = false;
		if ( $set != null ) {
			$this->setFormsConf( $set );
			$this->setDefaultStyleClasses();
		}
	}

	public function setDefaultStyleClasses(): void {
		$this->classes = [ 'form' => 'letter-form-container', 'letter-form-group' => 'letter-form-group' ];
	}

	public function setFormsConf( $set ): bool {
		if ( is_array( $set ) && ! empty( $set['fields'] ) ) {
			$this->config      = $set;
			$this->configIsSet = true;

			return true;
		} else {
			$this->configIsSet = false;

			return false;
		}
	}

	public function setCustomStyleClasses( $element, $class ): void {
		if ( isset( $this->classes[ $element ] ) && ! empty( $class ) ) {
			$this->classes[ $element ] = $class;
		}
	}

	public function constructHTML(): void {
		$out = '<form class="' . $this->classes['form'] . '" ';
		foreach ( $this->config['form'] as $key => $value ) {
			switch ( $key ) {
				case 'id':
				case 'name':
					$out = $out . $key . '=' . $value . '" ';
					break;
				case 'method' :
					if ( $value === 'POST' || $value === 'GET' ) {
						$out = $out . $key . '=' . $value . '" ';
					}
					break;
				default:
			}
		}
		$out = $out . '>';

		foreach ( $this->config['fields'] as $field ) {
			$out = $out . $this->constructField($field);
		}

		$this->htmlForm = $out . '</form>';
	}

	public function getHTML () {
		return $this->htmlForm;
	}

	private function constructField( $field ): string {
		$validTypes = [
			'checkbox', 'text', 'number', 'select', 'radio', 'button',
			'color', 'date', 'datetime-local', 'email', 'file', 'hidden',
			'image', 'password', 'range', 'reset', 'search', 'submit',
			'tel', 'time', 'url', 'week', 'month'
		];
		if (empty ($field['name']) && empty ($field['id'])) {
			return '';
		}
		$fieldHTML = '<input';
		foreach ( $field as $key => $value ) {
			switch ($key){
				case 'label':
					if (!empty($value) && !empty($field['id'])){
						$fieldHTML = '<label for="'.$field['id'].'">'.$value.'</label>' . $fieldHTML;
					}
					break;
				case 'type':
					if (in_array($value, $validTypes)){
						$fieldHTML = $fieldHTML . ' type="'.$value.'"';
					}
					else{
						$fieldHTML = $fieldHTML . ' type="text"';
					}
					break;
				default:  $fieldHTML = $fieldHTML . ' ' . $key . '="' . $value .'"';
			}
		}
		return '<div class="'.$this->classes['letter-form-group'].'">' . $fieldHTML .'</div>';
	}



}