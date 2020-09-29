<?php

namespace kbamarketing\smartproperties\models;

use Craft;
use craft\base\Model;

use kbamarketing\smartproperties\models\SmartProperties_CollectionModel as Collection;
use NumberFormatter;

class SmartProperties_BaseModel extends Model {
	
	const LOCALE = 'en_GB';
	const CURRENCY = 'GBP';
	
	protected $attributes = [];
	protected $_attributes = [];

	protected function getChildCollection( $child, $property ) {
		
		$elements = [];
		
		foreach( $this->getProperty($child) as $el ) {

			$elements = array_merge($elements, $el->getProperty($property)->all());
			
		}
		
		return new Collection($elements);
		
	}
	
	protected function defineAttributes() {
		
		return $this->attributes;
		
	}
	
	protected function setAttribute($key, $value) {
		
		$this->setAttributes([$key => $value]);
		
	}
	
	protected function getFormatter() {
		
		return new NumberFormatter( static::LOCALE, NumberFormatter::CURRENCY );
		
	}
	
	protected function formatCurrency( $price ) {
		
		$formatter = $this->getFormatter();
		
		$formatter->setAttribute( NumberFormatter::FRACTION_DIGITS, 0 );
		
		return $formatter->formatCurrency( $price, static::CURRENCY );
		
	}
	
	protected function setPrivateAttribute($key, $value) {
		
		$this->_attributes[$key] = $value;
		
	}
	
	protected function getProperty($property) {
		
		$value = $this->getAttribute($property);
		
		return $value ? $value : $this->getPrivateAttribute($property);
		
	}
	
	protected function getPrivateAttribute($key) {
		
		return ! empty( $this->_attributes[$key] ) ? $this->_attributes[$key] : null;
		
	}
	
	public function flatten()
	{
		$attributes = $this->getAttributes(null, true);
		foreach( $attributes as &$attribute ) {
			if( $attribute instanceof SmartProperties_BaseModel ) {
				$attribute = $attribute->flatten();
			} else if ( $attribute instanceof Model ) {
				$attribute = $attribute->getAttributes(null, true);
			} else if ( $attribute instanceof Collection ) {
				$attribute = $attribute->map(function($item) {
					if( $item instanceof SmartProperties_BaseModel ) {
						$item = $item->flatten();
					}
					return $item;
				})->toArray();
			}
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
			
		}
		
		return parent::__call( $name, $parameters );
		
	}
	
	
}
