<?php

namespace kbamarketing\smartproperties\models;

use Craft;

use kbamarketing\smartproperties\models\SmartProperties_CollectionModel as Collection;
use kbamarketing\smartproperties\models\SmartProperties_PropertyModel as Property;

class SmartProperties_PlotModel extends SmartProperties_BaseModel {
	
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
	
	public static function compile( array $data, Property $property ) {
		
		$plot = new static();
		
		$plot->setPrivateAttribute('block', $property->getPrivateAttribute('block'));
		$plot->setPrivateAttribute('floorplan', $property->getAttribute('floorplan'));
		
		$data = new Collection($data);
		
		$plot->setAttribute('data', $data);
		$plot->setAttribute('id', $data->get('plotNumber'));
		$plot->setAttribute('blockId', $property->getAttribute('blockId'));
		$plot->setAttribute('entryId', $property->getAttribute('entryId'));
		$plot->setAttribute('title', $property->getAttribute('title'));
		$plot->setAttribute('propertyType', $property->getAttribute('propertyType'));
		$plot->setAttribute('propertyId', $property->getAttribute('id'));
		$plot->setAttribute('floor', $data->get('floor') ? $data->get('floor') : ( $property->getAttribute('propertyType') == 'Apartment' ? $property->getAttribute('title') : 'ground' ));
		$plot->setAttribute('colour', $data->get('colour') ? $data->get('colour') : $property->getAttribute('colour'));
		$plot->setAttribute('isStudio', $property->getAttribute('defaultBedrooms') == static::STUDIO_LABEL);
		$plot->setAttribute('hidden', $data->get('hidden') ? true : false);
		
		$bedrooms = $plot->getAttribute('data')->getAttribute('numberOfBedrooms');
		$plot->setAttribute('numberOfBedrooms', is_numeric( $bedrooms ) ? $bedrooms : max($property->getAttribute('defaultBedrooms'), 1));
		
		$plot->setAttribute('price', $plot->getFormatter()->parse( $plot->getAttribute('data')->getAttribute('availability') ));
		$plot->setAttribute('formattedPrice', $plot->getProperty('price') ? $plot->formatCurrency( $plot->getProperty('price') ) : null);
		
		$dimensions = new Collection( $property->getAttribute('dimensions') );
		
		$plot->setAttribute('dimensions', $dimensions->filter(function(array $dimension) {
			
			return ! empty( $dimension['plotNumber'] ) && $dimension['plotNumber'] == $plot->getAttribute('data')->getAttribute('plotNumber');
			
		}));
		
		$plot->setAttribute('isAvailable', $plot->getAttribute('price') && ! $plot->getAttribute('hidden') ? true : false);
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
	
	protected function getFloors() {
		
		return property_exists($this->getAttribute('data'), 'floor') ? $this->getAttribute('data')->getAttribute('floor') : array_values(array_filter([$this->getBlock()->getFieldValue('floor'), $this->floor]))[0];
		
	}
	
}
