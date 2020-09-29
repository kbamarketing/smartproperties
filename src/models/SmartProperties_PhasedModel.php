<?php

namespace KBAMarketing\SmartProperties\models;

use KBAMarketing\SmartProperties\SmartProperties;

use Craft;

use SmartProperties_CollectionModel as Collection;
use SmartProperties_PhaseModel as Phase;

class SmartProperties_PhasedModel extends SmartProperties_ContainerModel {
	
	protected $priceFromLabel = 'From';
	
	protected function defineAttributes() {
		return array_merge($this->attributes, array(
			'phases' => AttributeType::Mixed
		));
	}
	
	protected function beforeCompile( EntryModel $entry ) {
		
		$this->setAttribute('phases', $this->getPhases( $entry ));
		$this->setAttribute('isPhased', true);
		
	}
	
	protected function getPhases( EntryModel $entry ) {
		
		return new Collection( array_map( function( EntryModel $child ) { 
			
			return Phase::compile( $child, true );
			
		}, $entry->getChildren()->find() ) );
		
	}
	
	protected function getProperties( EntryModel $entry ) {
		
		return $this->getChildCollection( 'phases', 'properties' );
		
	}
		
}