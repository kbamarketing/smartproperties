<?php

namespace kbamarketing\smartproperties\models;

use Craft;

use kbamarketing\smartproperties\models\SmartProperties_PlotModel as Plot;
use kbamarketing\smartproperties\models\SmartProperties_CollectionModel as Collection;
use kbamarketing\smartproperties\models\SmartProperties_HasBedroomsModel as HasBedrooms;
use kbamarketing\smartproperties\models\SmartProperties_HasPlotsModel as HasPlots;
use kbamarketing\smartproperties\models\AttributeType;

class SmartProperties_PropertyModel extends SmartProperties_BaseModel {

	use HasPlots, HasBedrooms {
		getBedrooms as getTraitBedrooms;
	}

	protected $attributes = array(
		'id' => AttributeType::Number,
		'blockId' => AttributeType::Number,
		'blockType' => AttributeType::String,
		'entryId' => AttributeType::Number,
		'propertyType' => AttributeType::String,
		'floorplan' => AttributeType::String,

		'colour' => AttributeType::String,
		'hasFloorplan' => AttributeType::Bool,
		'title' => AttributeType::String,
		'defaultBedrooms' => AttributeType::Number,
		'bedrooms' => AttributeType::Number,
		'availableBedrooms' => AttributeType::Number,
		'minBedrooms' => AttributeType::Number,
		'maxBedrooms' => AttributeType::Number,
		'minAvailableBedrooms' => AttributeType::Number,
		'maxAvailableBedrooms' => AttributeType::Number,
		'hasVariatingBedrooms' => AttributeType::Bool,
		'hasVariatingAvailableBedrooms' => AttributeType::Bool,
		'hasVariatingPrices' => AttributeType::Bool,
		'bedroomsHtml' => AttributeType::String,
		'availableBedroomsHtml' => AttributeType::String,
		'isStudio' => AttributeType::Bool,
		'image' => AttributeType::Mixed,
		'floorplan' => AttributeType::Mixed,
		'cfloor' => AttributeType::Mixed,

		'availabilityHtml' => AttributeType::String,
		'hasPrices' => AttributeType::Bool,
		'hasPlots' => AttributeType::Bool,
		'plotsHtml' => AttributeType::String,
		'availablePlotsHtml' => AttributeType::String,
		'priceHtml' => AttributeType::String,
		'floorplanNotes' => AttributeType::String,
		'dimensions' => AttributeType::Mixed
	);

	public static function compile( craft\elements\MatrixBlock $block, $entryId ) {

		$property = new static();

		$property->setAttribute('id', $block->getAttribute('id'));
		$property->setAttribute('blockId', $block->getAttribute('id'));
		$property->setAttribute('blockType', $block->type->getAttribute('handle'));
		$property->setAttribute('entryId', $entryId);
		$property->setAttribute('propertyType', array_values(array_filter([$block->getContent()->getAttribute('propertyType'), Plot::APARTMENT_LABEL]))[0]);
		$property->setAttribute('title', $block->getFieldValue('theTitle'));
		$property->setAttribute('defaultBedrooms', $block->getContent()->getAttribute('numberOfBedrooms'));
		$property->setAttribute('hasFloorplan', $block->getFieldValue('floorplan')->first() ? true : false);
		$property->setAttribute('floorplan', $block->getFieldValue('floorplan')->first());

		$property->setAttribute('cfloor', array_key_exists('cfloor', $block->getContent()->getAttributes()) ? $block->getFieldValue('cfloor') : null);
		$property->setAttribute('colour', array_key_exists('colour', $block->getContent()->getAttributes()) ? $block->getFieldValue('colour') : null);
		$property->setAttribute('floorplanNotes', $block->getContent()->getAttribute('floorplanNotes'));
		$property->setAttribute('image', array_key_exists('image', $block->getContent()->getAttributes()) ? $block->getFieldValue('image') : null);

		$property->setPrivateAttribute('block', $block);

		$plots = [];
		foreach($block->getFieldValue('plots') as $plot) {
			$plots[] = $plot->getContent()->getAttributes();
		}

		$property->setPrivateAttribute('plots', new Collection( array_map( function( $plot ) use($property) {
			return Plot::compile( $plot, clone $property );
		}, $plots ) ) );

		$property->setAttribute('isStudio', $property->isStudio());
		$property->setPrivateAttribute('availablePlots', $property->getAvailablePlots());
		$property->setAttribute('bedrooms', $property->getBedrooms());
		$property->setAttribute('availableBedrooms', $property->getAvailableBedrooms());
		$property->setAttribute('minBedrooms', $property->getMinBedrooms());
		$property->setAttribute('maxBedrooms', $property->getMaxBedrooms());
		$property->setAttribute('minAvailableBedrooms', $property->getMinAvailableBedrooms());
		$property->setAttribute('maxAvailableBedrooms', $property->getMaxAvailableBedrooms());
		$property->setAttribute('hasVariatingBedrooms', $property->hasVariatingBedrooms());
		$property->setAttribute('hasVariatingAvailableBedrooms', $property->hasVariatingAvailableBedrooms());
		$property->setAttribute('hasVariatingPrices', $property->hasVariatingPrices());
		$property->setAttribute('bedroomsHtml', $property->getBedroomsHtml());
		$property->setAttribute('availableBedroomsHtml', $property->getAvailableBedroomsHtml());
		$property->setAttribute('availabilityHtml', $property->getAvailabilityHtml());
		$property->setAttribute('hasPrices', $property->hasPrices());
		$property->setAttribute('hasPlots', $property->getPlots() ? true : false);
		$property->setAttribute('plotsHtml', $property->getPlots()->concat('id'));
		$property->setAttribute('availablePlotsHtml', $property->getAvailablePlots()->count());
		$property->setAttribute('priceHtml', $property->getPriceHtml());
		$property->setAttribute('dimensions', $block->getFieldValue('dimensions'));

		return $property;

	}

	public function getBedrooms() {

		$bedrooms = $this->getTraitBedrooms();

		return $bedrooms->count() ? $bedrooms : new Collection( [ $this->getAttribute('defaultBedrooms') ] );

	}

}
