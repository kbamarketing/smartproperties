<?php

namespace kbamarketing\smartproperties\models;

use Craft;

use kbamarketing\smartproperties\models\SmartProperties_CollectionModel as Collection;
use NumberFormatter;

class SmartProperties_BaseModel {
	
	const LOCALE = 'en_GB';
	const CURRENCY = 'GBP';
	
	protected $attributes = [];
	protected $_attributes = [];
	
	public function __construct($data = [], $private = []) {
		
		$this->attributes = new Collection($data);
		$this->_attributes = new Collection($private);
		
	}
	
	protected function setAttribute($key, $value) {
		
		$this->attributes[$key] = $value;
		
	}
	
	protected function getAttribute($key) {
		
		return $this->attributes->get($key);
		
	}
	
	protected function setPrivateAttribute($key, $value) {
		
		$this->_attributes[$key] = $value;
		
	}
	
	protected function getProperty($property) {
		
		$value = $this->getAttribute($property);
		
		return $value ? $value : $this->getPrivateAttribute($property);
		
	}
	
	protected function getPrivateAttribute($key) {
		
		return $this->_attributes->get($key);
		
	}

	protected function getChildCollection( $child, $property ) {
		
		$elements = [];
		
		foreach( $this->getProperty($child) as $el ) {

			$elements = array_merge($elements, $el->getProperty($property)->all());
			
		}
		
		return new Collection($elements);
		
	}
		
	protected function getFormatter() {
		
		return new NumberFormatter( static::LOCALE, NumberFormatter::CURRENCY );
		
	}
	
	protected function formatCurrency( $price ) {
		
		$formatter = $this->getFormatter();
		
		$formatter->setAttribute( NumberFormatter::FRACTION_DIGITS, 0 );
		
		return $formatter->formatCurrency( $price, static::CURRENCY );
		
	}
	
	public function flatten()
	{
		$attributes = [];
		foreach( $this->attributes as $key => $attribute ) {	
			if( $attribute instanceof SmartProperties_BaseModel ) {
				$attribute = $attribute->flatten();
			} else if ( $attribute instanceof Collection ) {
				$attribute = $attribute->map(function($item) {
					if( $item instanceof SmartProperties_BaseModel ) {
						$item = $item->flatten();
					}
					return $item;
				})->toArray();
			}
			$attributes[$key] = $attribute;
		}
		return $attributes;
	}
	
	public function __call( $name, $parameters ) {
		
		preg_match('/get(.*)/', $name, $matches);
		
		if( $matches ) {
			
			$property = strtolower( $matches[1] );
		
			if( property_exists( $this, $property ) ) {
				
				return $this->$property;
				
			}
			
		} else {
			
			return $this->getAttribute($name);
			
		}
		
	}
	
}
