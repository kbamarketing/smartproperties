<?php

namespace kbamarketing\smartproperties\models;


use Craft;

use kbamarketing\smartproperties\models\SmartProperties_CollectionModel as Collection;
use kbamarketing\smartproperties\models\SmartProperties_HasBedroomsModel as HasBedrooms;
use kbamarketing\smartproperties\models\SmartProperties_HasPlotsModel as HasPlots;
use kbamarketing\smartproperties\models\SmartProperties_PlotModel as Plot;
use kbamarketing\smartproperties\models\SmartProperties_PropertyModel as Property;

class SmartProperties_FloorModel extends SmartProperties_BaseModel {
	
	use HasBedrooms, HasPlots;
			
	public static function compile( craft\elements\MatrixBlock $block, Collection $plots ) {	
		
		$floor = new static();
		
		$floor->setAttribute('blockId', $block->id);
		
		$floor->setPrivateAttribute('plots', $plots);
		
		$floor->setAttribute('title', static::determineTitle( $block ));
		$floor->setAttribute('floorplan', $floor->getPlots()->first()->getProperty('floorplan') ? $floor->getPlots()->first()->getProperty('floorplan') : null );
		$floor->setAttribute('image', array_key_exists('image', $block->getFieldValues()) ? $block->getFieldValue('image')->first() : $floor->getAttribute('floorplan'));
		$floor->setAttribute('availableProperties', $floor->getAvailableProperties()->map(array($floor, 'mapProperty'))->unique());
		$floor->setAttribute('properties', $floor->getProperties()->map(array($floor, 'mapProperty'))->unique());
		$floor->setAttribute('availablePlots', $floor->getAvailablePlots());
		
		$floor->setAttribute('availablePlotsHtml', $floor->getAvailablePlotsHtml());
		$floor->setAttribute('bedrooms', $floor->getBedrooms());
		$floor->setAttribute('minBedrooms', $floor->getMinBedrooms());
		$floor->setAttribute('maxBedrooms', $floor->getMaxBedrooms());
		$floor->setAttribute('hasVariatingBedrooms', $floor->hasVariatingBedrooms());
		$floor->setAttribute('bedroomsHtml', $floor->getAvailableNumericBedrooms()->concat());
		$floor->setAttribute('availableBedroomsHtml', $floor->getAvailableBedroomsHtml());
		
		return $floor;
		
	}
	
	public function mapProperty(Property $property) {
		
		return [
			'id' => $property->getAttribute('id'),
			'propertyType' => $property->getAttribute('propertyType'),
			'blockType' => $property->getAttribute('blockType')
		];
		
	}
	
	protected function getProperties() {
		
		return $this->getPlots()->map(function(Plot $plot) {
			
			return Property::compile( $plot->getPrivateAttribute('block'), $plot->getPrivateAttribute('entryId') );
			
		})->keyBy('propertyType')->sort();
		
	}

	protected function getAvailableProperties() {
		
		return $this->getAvailablePlots()->map(function(Plot $plot) {
			
			return Property::compile( $plot->getPrivateAttribute('block'), $plot->getPrivateAttribute('entryId') );
			
		})->keyBy('propertyType')->sort();
		
	}
	
	public static function determineTitle( craft\elements\MatrixBlock $block ) {
		
		return is_string( $block->getFieldValue('theTitle') ) ? $block->getFieldValue('theTitle') : $block->getFieldValue('theTitle')->label;
		
	}
	
}