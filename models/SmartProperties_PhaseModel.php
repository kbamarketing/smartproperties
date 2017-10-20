<?php

namespace Craft;

use Craft\SmartProperties_CollectionModel as Collection;
use Craft\SmartProperties_PropertyModel as Property;
use Craft\SmartProperties_PlotModel as Plot;
use Craft\SmartProperties_FloorModel as PropertyFloor;

class SmartProperties_PhaseModel extends SmartProperties_ContainerModel {
	
	protected function defineAttributes() {
		return array_merge($this->attributes, array(
			'floors' => AttributeType::Mixed
		));
	}
	
	protected function afterCompile( EntryModel $entry ) {
		
		$this->setAttribute('floors', $this->getFloors( $entry ));
		
	}
	
	protected function getProperties( EntryModel $entry ) {
		
		return new Collection( array_map( function( MatrixBlockModel $block ) {
			
			return Property::compile( $block, $this->getAttribute('id') );
			
		}, array_merge( 
			$entry->getFieldValue( 'developmentHomeTypes' )->find(),
			$entry->getFieldValue( 'developmentApartmentFloors' )->find()
		) ) );
		
	}
	
	protected function getFloors( EntryModel $entry ) {
		
		$blocks = array_merge( 
			$entry->getFieldValue( 'developmentFloors' )->find(),
			$entry->getFieldValue( 'developmentApartmentFloors' )->find() 
		);
		
		$collection = new Collection();
		
		foreach($blocks as $block) {
			
			$plots = $this->getPlots()->filter( function( Plot $plot ) use( $block ) {
				
				$title = PropertyFloor::determineTitle( $block );
			
				return strpos( $title, $plot->getAttribute('floor') ) === 0;	
				
			} );
			
			if( $plots->count() ) {
				
				$collection->push(PropertyFloor::compile( $block, $plots ));
				
			}
			
		}
		
		return $collection;
		
	}
	
}