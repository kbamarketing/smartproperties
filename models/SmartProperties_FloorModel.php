<?php

namespace Craft;

use Craft\SmartProperties_CollectionModel as Collection;
use Craft\SmartProperties_HasBedroomsModel as HasBedrooms;
use Craft\SmartProperties_HasPlotsModel as HasPlots;
use Craft\SmartProperties_PlotModel as Plot;
use Craft\SmartProperties_PropertyModel as Property;

class SmartProperties_FloorModel extends SmartProperties_BaseModel {
	
	use HasBedrooms, HasPlots;
	
	protected $attributes = array(
		'id' => AttributeType::Number,
		'blockId' => AttributeType::Number,
		'title' => AttributeType::String,
		'floorplan' => AttributeType::String,
		'availableProperties' => AttributeType::Mixed,
		'availablePlots' => AttributeType::Mixed,
		'availablePlotsHtml' => AttributeType::String,
		'bedrooms' => AttributeType::Number,
		'minBedrooms' => AttributeType::Number,
		'maxBedrooms' => AttributeType::Number,
		'hasVariatingBedrooms' => AttributeType::Bool,
		'bedroomsHtml' => AttributeType::String,
		'availableBedroomsHtml' => AttributeType::String
	);
			
	public static function compile( MatrixBlockModel $block, Collection $plots ) {	
		
		$floor = new static();
		
		$floor->setAttribute('blockId', $block->getAttribute('id'));
		
		$floor->setPrivateAttribute('plots', $plots);
		
		$floor->setAttribute('title', static::determineTitle( $block ));
		$floor->setAttribute('floorplan', $block->getContent()->getAttribute('floorplan') ? $block->getFieldValue('floorplan')->first() : ( $floor->getPlots()->first()->getAttribute('floorplan') ? $floor->getPlots()->first()->getAttribute('floorplan') : null ));
		$floor->setAttribute('availableProperties', $floor->getAvailableProperties()->map(function(Property $property) {
			return [
				'id' => $property->getAttribute('id'),
				'propertyType' => $property->getAttribute('propertyType'),
				'blockType' => $property->getAttribute('blockType')
			];
		}));
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

	protected function getAvailableProperties() {
		
		return (new Collection($this->getAvailablePlots()->map(function(Plot $plot) {
			
			return Property::compile( $plot->getPrivateAttribute('block'), $plot->getPrivateAttribute('entryId') );
			
		})))->unique()->keyBy('propertyType')->sort();
		
	}
	
	public static function determineTitle( MatrixBlockModel $block ) {
		
		return is_string( $block->getFieldValue('theTitle') ) ? $block->getFieldValue('theTitle') : $block->getFieldValue('theTitle')->label;
		
	}
	
}