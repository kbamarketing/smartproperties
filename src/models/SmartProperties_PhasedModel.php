<?php

namespace kbamarketing\smartproperties\models;

use Craft;

use kbamarketing\smartproperties\models\SmartProperties_CollectionModel as Collection;
use kbamarketing\smartproperties\models\SmartProperties_PhaseModel as Phase;
use kbamarketing\smartproperties\models\AttributeType;

class SmartProperties_PhasedModel extends SmartProperties_ContainerModel {
	
	protected $priceFromLabel = 'From';
	
	protected function beforeCompile( \craft\elements\Entry $entry ) {
		
		$this->setAttribute('phases', $this->getPhases( $entry ));
		$this->setAttribute('isPhased', true);
		
	}
	
	protected function getPhases( \craft\elements\Entry $entry ) {
		
		return new Collection( array_map( function( \craft\elements\Entry $child ) { 
			
			return Phase::compile( $child, true );
			
		}, $entry->getChildren()->find() ) );
		
	}
	
	protected function getProperties( \craft\elements\Entry $entry ) {
		
		return $this->getChildCollection( 'phases', 'properties' );
		
	}
		
}