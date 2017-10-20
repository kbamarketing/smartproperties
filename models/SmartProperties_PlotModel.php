<?php

namespace Craft;

use Craft\SmartProperties_CollectionModel as Collection;
use Craft\SmartProperties_HasBlockModel as HasBlock;
use Craft\SmartProperties_FlexibleModel as Model;
use Craft\SmartProperties_PropertyModel as Property;

class SmartProperties_PlotModel extends SmartProperties_BaseModel {
	
	use HasBlock;
	
	const SOLD_LABEL = 'Sold';
	const RESERVED_LABEL = 'Reserved';
	const ALL_SOLD_LABEL = 'All sold';
	const ALL_RESERVED_LABEL = 'All reserved';
	const TO_BE_RELEASED_LABEL = 'To be released';
	const PRICE_FROM_LABEL = 'Prices from';
	const STUDIO_LABEL = 'Studio';
	const HELP_TO_BUY_LABEL = 'Help to Buy';
	const AVAILABLE_LABEL = 'Available';
	const APARTMENT_LABEL = 'Apartment';
	const GROUND_FLOOR_LABEL = 'Ground';
	
	protected $attributes = array(
		'id' => AttributeType::Number,
		'blockId' => AttributeType::Number,
		'entryId' => AttributeType::Number,
		'title' => AttributeType::String,
		'propertyType' => AttributeType::String,
		'propertyId' => AttributeType::Number,
		'data' => AttributeType::Mixed,
		'floor' => AttributeType::String,
		'numberOfBedrooms' => AttributeType::Number,
		'price' => AttributeType::Number,
		'formattedPrice' => AttributeType::String,
		'isAvailable' => AttributeType::Bool,
		'toBeReleased' => AttributeType::Bool,
		'hasDimensions' => AttributeType::Bool,
		'dimensions' => AttributeType::Mixed,
		'hasFloorplan' => AttributeType::Bool
	);
	
	public static function compile( array $data, Property $property ) {
		
		$plot = new static();
		
		$data = new Model($data);
		
		$plot->setAttribute('data', $data);
		
		$plot->setAttribute('id', $data->getAttribute('plotNumber'));
		$plot->setPrivateAttribute('block', $property->getPrivateAttribute('block'));
		$plot->setAttribute('blockId', $property->getAttribute('blockId'));
		$plot->setAttribute('entryId', $property->getAttribute('entryId'));
		$plot->setAttribute('title', $property->getAttribute('title'));
		$plot->setAttribute('propertyType', $property->getAttribute('propertyType'));
		$plot->setAttribute('propertyId', $property->getAttribute('id'));
		$plot->setAttribute('floor', $data->getAttribute('floor') ? $data->getAttribute('floor') : $property->getAttribute('title'));
		$plot->setAttribute('numberOfBedrooms', $plot->getNumberOfBedrooms( $property ));
		
		$plot->setAttribute('price', $plot->getPrice());
		$plot->setAttribute('formattedPrice', $plot->getFormattedPrice());
		
		$plot->setAttribute('dimensions', $plot->getDimensions( $property ));
		
		$plot->setAttribute('isAvailable', $plot->getAttribute('price') ? true : false);
		$plot->setAttribute('toBeReleased', $plot->is(static::TO_BE_RELEASED_LABEL));
		$plot->setAttribute('hasDimensions', $plot->getAttribute('dimensions')->count() ? true : false);
		
		$plot->setAttribute('hasFloorplan', $property->getAttribute('hasFloorplan'));
		
		return $plot;
		
	}
	
	public function __toString() {
		
		return $this->getAttribute('data')->getAttribute('plotNumber');
		
	}
	
	public function is( $availability ) {
		
		return strtolower(trim($this->getAttribute('data')->getAttribute('availability'))) === strtolower(trim($availability));
		
	}
	
	protected function getPrice() {

		return $this->getFormatter()->parse( $this->getAttribute('data')->getAttribute('availability') );
		
	}
	
	protected function getFormattedPrice() {
		
		return $this->getPrice() ? $this->formatCurrency( $this->getPrice() ) : null;
		
	}
	
	protected function getFloors() {
		
		return property_exists($this->getAttribute('data'), 'floor') ? $this->getAttribute('data')->getAttribute('floor') : array_values(array_filter([$this->getBlock()->getFieldValue('floor'), $this->floor]))[0];
		
	}
	
	protected function getNumberOfBedrooms( Property $property ) {
		
		$bedrooms = $this->getAttribute('data')->getAttribute('numberOfBedrooms');
		
		return is_numeric( $bedrooms ) ? $bedrooms : max($property->getAttribute('defaultBedrooms'), 1);
		
	}
	
	protected function getDimensions( Property $property ) {
		
		$dimensions = new Collection( $property->getAttribute('dimensions') );
			
		return $dimensions->filter(function(array $dimension) {
			
			return ! empty( $dimension['plotNumber'] ) && $dimension['plotNumber'] == $this->getAttribute('data')->getAttribute('plotNumber');
			
		});
		
	}
	
}