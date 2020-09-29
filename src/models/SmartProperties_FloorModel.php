<?php

namespace kbamarketing\smartproperties\models;


use Craft;

use kbamarketing\smartproperties\models\SmartProperties_CollectionModel as Collection;
use kbamarketing\smartproperties\models\SmartProperties_HasBedroomsModel as HasBedrooms;
use kbamarketing\smartproperties\models\SmartProperties_HasPlotsModel as HasPlots;
use kbamarketing\smartproperties\models\SmartProperties_PlotModel as Plot;
use kbamarketing\smartproperties\models\SmartProperties_PropertyModel as Property;
use kbamarketing\smartproperties\models\AttributeType;

class SmartProperties_FloorModel extends SmartProperties_BaseModel {
	
	use HasBedrooms, HasPlots;
	
	protected $attributes = array(
		'id' => AttributeType::Number,
		'blockId' => AttributeType::Number,
		'title' => AttributeType::String,
		'floorplan' => AttributeType::String,
		'image' => AttributeType::String,
		'availableProperties' => AttributeType::Mixed,
		'properties' => AttributeType::Mixed,
		'availablePlots' => AttributeType::Mixed,
		'availablePlotsHtml' => AttributeType::String,
		'bedrooms' => AttributeType::Number,
		'minBedrooms' => AttributeType::Number,
		'maxBedrooms' => AttributeType::Number,
		'hasVariatingBedrooms' => AttributeType::Bool,
		'bedroomsHtml' => AttributeType::String,
		'availableBedroomsHtml' => AttributeType::String
	);
			
	public static function compile( craft\elements\MatrixBlock $block, Collection $plots ) {	
		
		$floor = new static();
		
		$floor->setAttribute('blockId', $block->getAttribute('id'));
		
		$floor->setPrivateAttribute('plots', $plots);
		
		$floor->setAttribute('title', static::determineTitle( $block ));
		$floor->setAttribute('floorplan', $floor->getPlots()->first()->getProperty('floorplan') ? $floor->getPlots()->first()->getProperty('floorplan') : null );
		$floor->setAttribute('image', array_key_exists('image', $block->getContent()->getAttributes()) ? $block->getFieldValue('image')->first() : $floor->getAttribute('floorplan'));
		$floor->setAttribute('availableProperties', $floor->getAvailableProperties()->map(array($floor, 'mapProperty')));
		$floor->setAttribute('properties', $floor->getProperties()->map(array($floor, 'mapProperty')));
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
		
		return (new Collection($this->getPlots()->map(function(Plot $plot) {
			
			return Property::compile( $plot->getPrivateAttribute('block'), $plot->getPrivateAttribute('entryId') );
			
		})))->unique()->keyBy('propertyType')->sort();
		
	}

	protected function getAvailableProperties() {
		
		return (new Collection($this->getAvailablePlots()->map(function(Plot $plot) {
			
			return Property::compile( $plot->getPrivateAttribute('block'), $plot->getPrivateAttribute('entryId') );
			
		})))->unique()->keyBy('propertyType')->sort();
		
	}
	
	public static function determineTitle( craft\elements\MatrixBlock $block ) {
		
		return is_string( $block->getFieldValue('theTitle') ) ? $block->getFieldValue('theTitle') : $block->getFieldValue('theTitle')->label;
		
	}
	
}