<?php

namespace KBAMarketing\SmartProperties\models;

use KBAMarketing\SmartProperties\SmartProperties;

use Craft;

use SmartProperties_CollectionModel as Collection;
use SmartProperties_PropertyModel as Property;
use SmartProperties_PlotModel as Plot;
use SmartProperties_FloorModel as PropertyFloor;

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
			
				return strpos( strtolower( $title ), strtolower( $plot->getAttribute('floor') ) ) === 0;	
				
			} );
			
			if( $plots->count() ) {
				
				$collection->push(PropertyFloor::compile( $block, $plots ));
				
			}
			
		}
		
		return $collection;
		
	}
	
}