<?php

namespace Craft;

use Craft\SmartProperties_HasBedroomsModel as HasBedrooms;
use Craft\SmartProperties_HasPlotsModel as HasPlots;
use Craft\SmartProperties_PlotModel as Plot;

abstract class SmartProperties_ContainerModel extends SmartProperties_BaseModel {
	
	use HasBedrooms, HasPlots;
	
	protected $attributes = array(
		'id' => AttributeType::Number,
		'entryId' => AttributeType::Number,
		'slug' => AttributeType::String,
		'title' => AttributeType::String,
		'isPhased' => AttributeType::Bool,
		'isHtb' => AttributeType::Bool,
		'properties' => AttributeType::Mixed,
		'plots' => AttributeType::Mixed,
		'hasPrices' => AttributeType::Bool,
		'minPrice' => AttributeType::Number,
		'maxPrice' => AttributeType::Number,
		'minAvailableBedrooms' => AttributeType::Number,
		'bedroomsHtml' => AttributeType::String,
		'availableBedroomsHtml' => AttributeType::String,
		'hasVariatingPrices' => AttributeType::Bool,
		'priceHtml' => AttributeType::String,
		'availabilityHtml' => AttributeType::String,
		'isAvailable' => AttributeType::Bool
	);
	
	public static function compile( EntryModel $entry, $isChild = false ) {
		
		$container = new static();
		
		$container->beforeCompile( $entry );
		
		$container->setAttribute('id', $entry->id);
		
		$container->setAttribute('entryId', $entry->id);
		$container->setAttribute('slug', $entry->slug);
		$container->setAttribute('title', $entry->title);
		$container->setPrivateAttribute('properties', $container->getProperties( $entry ));
		
		$plots = $container->getChildCollection( 'properties', 'plots' );
		
		$plots = $plots->sort(function($a, $b) {
				
			return $a->getAttribute('data')->getAttribute('plotNumber') > $b->getAttribute('data')->getAttribute('plotNumber') ? 1 : ( $a->getAttribute('data')->getAttribute('plotNumber') == $b->getAttribute('data')->getAttribute('plotNumber') ? 0 : -1 );
			
		});
		
		$container->setPrivateAttribute('plots', $plots);
		
		if( ! $isChild ) {
			
			$container->setAttribute('plots', $container->getPrivateAttribute('plots'));
			$container->setAttribute('properties', $container->getPrivateAttribute('properties'));
			
		}
		
		$container->setAttribute('hasPrices', $container->hasPrices());
		$container->setAttribute('minPrice', $container->getMinPrice());
		$container->setAttribute('maxPrice', $container->getMaxPrice());
		
		$container->setAttribute('minBedrooms', $container->getMinBedrooms());
		$container->setAttribute('maxBedrooms', $container->getMaxBedrooms());
		$container->setAttribute('minAvailableBedrooms', $container->getMinAvailableBedrooms());
		$container->setAttribute('maxAvailableBedrooms', $container->getMaxAvailableBedrooms());
		
		$container->setAttribute('isHtb', in_array(Plot::HELP_TO_BUY_LABEL, array_column($entry->developmentSchemes->find(), 'title')));
		
		$container->setAttribute('bedroomsHtml', $container->getBedroomsHtml());
		$container->setAttribute('availableBedroomsHtml', $container->getAvailableBedroomsHtml());
		
		$container->setAttribute('hasVariatingPrices', $container->hasVariatingPrices());
		$container->setAttribute('priceHtml', $container->getPriceHtml());
		$container->setAttribute('availabilityHtml', $container->getAvailabilityHtml());
		$container->setAttribute('isAvailable', $container->isAvailable());
		
		$container->afterCompile( $entry );
		
		return $container;
		
	}
	
	protected function beforeCompile( EntryModel $entry ) {
		
		
		
	}
	
	protected function afterCompile( EntryModel $entry ) {
		
		
		
	}
	
	protected function getProperties( EntryModel $entry ) {
		
		return [];
		
	}
	
}