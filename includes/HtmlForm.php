<?php

namespace letter\includes;

class HtmlForm {
	public bool $configIsSet;
	private array $config;
	private array $classes;

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
			}
		}
		$out = $out . '>';

		foreach ( $this->config['fields'] as $field ) {
			$out = $out . $this->constructField($field);
		}

	}

	private function constructField( $field ): string {
		if (empty ($field['name']) && empty ($field['id'])) {
			return '';
		}
		$out = '<div class="'.$this->classes['letter-form-group'].'">';
		$fieldHTML = '<input';
		foreach ( $field as $key => $value ) {
			switch ($key){
				case 'label':
					if (!empty($value) && !empty($field['id'])){
						$fieldHTML = '<label for="'.$field['id'].'">'.$value.'</label>' . $fieldHTML;
					}
					break;
				case 'name':
				case 'id':
				case 'value':
					$fieldHTML = $fieldHTML . ' ' . $key . '="' . $value .'"';
					break;
				case 'type':
					if ($this->checkUpType($value)){
						$fieldHTML = $fieldHTML . ' type="'.$value.'"';
					}
					else{
						$fieldHTML = $fieldHTML . ' type="text"';
					}
					break;
			}

		}
	}
	private function checkUpType( string $value ) {
		switch ($value){
			case 'checkbox':
			case 'text':
			case 'number':
			case 'select':
			case 'radio':
			case 'button':
			case 'color':
			case 'date':
			case 'datetime-local':
			case 'email':
			case 'file':
			case 'hidden':
			case 'image':
			case 'password':
			case 'range':
			case 'reset':
			case 'search':
			case 'submit':
			case 'tel':
			case 'time':
			case 'url':
			case 'week':
			case 'month':
				return true;
			default: return false ;
		}
	}

}