<?php

namespace kbamarketing\smartproperties\models;

use Craft;

use kbamarketing\smartproperties\models\SmartProperties_CollectionModel as Collection;
use kbamarketing\smartproperties\models\SmartProperties_PropertyModel as Property;
use kbamarketing\smartproperties\models\SmartProperties_PlotModel as Plot;
use kbamarketing\smartproperties\models\SmartProperties_FloorModel as PropertyFloor;
use kbamarketing\smartproperties\models\AttributeType;

class SmartProperties_PhaseModel extends SmartProperties_ContainerModel {
	
	protected function defineAttributes() {
		return array_merge($this->attributes, array(
			'floors' => AttributeType::Mixed
		));
	}
	
	protected function afterCompile( \craft\elements\Entry $entry ) {
		
		$this->setAttribute('floors', $this->getFloors( $entry ));
		
	}
	
	protected function getProperties( \craft\elements\Entry $entry ) {
		
		$blocks = [];
		
		try {
			
			$blocks = array_merge($blocks, $entry->getFieldValue( 'developmentHomeTypes' )->find());
			$blocks = array_merge($blocks, $entry->getFieldValue( 'developmentApartmentFloors' )->find());
			
		} catch(\Exception $e) {
			
			
			
		}
		
		return new Collection( array_map( function( craft\elements\MatrixBlock $block ) {
			
			return Property::compile( $block, $this->getAttribute('id') );
			
		}, $blocks ) );
		
	}
	
	protected function getFloors( \craft\elements\Entry $entry ) {
		
		$blocks = [];
		
		try {
			
			$blocks = array_merge($blocks, $entry->getFieldValue( 'developmentHomeTypes' )->find());
			$blocks = array_merge($blocks, $entry->getFieldValue( 'developmentApartmentFloors' )->find());
			
		} catch(\Exception $e) {
			
			
			
		}
		
		$collection = new Collection();
		
		foreach($blocks as $block) {
			
			$plots = $this->getPlots()->filter( function( Plot $plot ) use( $block ) {
				
				$title = PropertyFloor::determineTitle( $block );
			
				return strpos( strtolower( $title ), strtolower( $plot->getAttribute('floor') ) ) === 0;	
				
			} );
			
			if( $plots->count() ) {
				
				$collection->push(PropertyFloor::compile( $block, $plots ));
				
			}
			
		}
		
		return $collection;
		
	}
	
}