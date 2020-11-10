<?php

namespace kbamarketing\smartproperties\models;


use Craft;

use kbamarketing\smartproperties\models\SmartProperties_CollectionModel as Collection;
use kbamarketing\smartproperties\models\SmartProperties_PlotModel as Plot;

trait SmartProperties_HasBedroomsModel {
	
	protected function getBedrooms() {
		
		return $this->getPlots()->map( function( Plot $plot ) { return $plot->getAttribute('numberOfBedrooms'); } )->filter()->unique();
		
	}
	
	protected function getAvailableBedrooms() {
		
		return $this->getAvailablePlots()->map( function( Plot $plot ) { return $plot->getAttribute('numberOfBedrooms'); } )->filter()->unique();
		
	}
	
	protected function getNumericBedrooms() {
		
		return $this->getBedrooms()->filter( function( $bedrooms ) {
		
			return is_numeric( $bedrooms );
			
		} );
		
	}
	
	protected function getAvailableNumericBedrooms() {
		
		return $this->getAvailableBedrooms()->filter( function( $bedrooms ) {
		
			return is_numeric( $bedrooms );
			
		} );
		
	}

	protected function getBedroomsHtml() {
		
		$parts = new Collection( array_merge( $this->getBedrooms()->contains( Plot::STUDIO_LABEL ) ? [ Plot::STUDIO_LABEL ] : [], $this->getBedrooms()->all() ) );
		
		return $parts->sort()->concat();
		
	}
	
	protected function getAvailableBedroomsHtml() {
		
		$parts = new Collection( array_merge( $this->getAvailableBedrooms()->contains( Plot::STUDIO_LABEL ) ? [ Plot::STUDIO_LABEL ] : [], $this->getAvailableNumericBedrooms()->all() ) );
		
		return $parts->sort()->concat();
		
	}
	
	protected function getMinBedrooms() {
		
		$bedrooms = $this->getNumericBedrooms()->all();
		
		return $bedrooms ? min( $bedrooms ) : 0;
		
	}
	
	protected function getMinAvailableBedrooms() {
		
		$bedrooms = $this->getAvailableNumericBedrooms()->all();
		
		return $bedrooms ? min( $bedrooms ) : 0;
		
	}
	
	protected function getMaxBedrooms() {
		
		$bedrooms = $this->getNumericBedrooms()->all();
		
		return $bedrooms ? max( $bedrooms ) : 0;
		
	}
	
	protected function getMaxAvailableBedrooms() {
		
		$bedrooms = $this->getAvailableNumericBedrooms()->all();
		
		return $bedrooms ? max( $bedrooms ) : 0;
		
	}
	
	protected function hasVariatingBedrooms() {
		
		return $this->getMinBedrooms() !== $this->getMaxBedrooms();
		
	}
	
	protected function hasVariatingAvailableBedrooms() {
		
		return $this->getMinAvailableBedrooms() !== $this->getMaxAvailableBedrooms();
		
	}
	
	protected function isStudio() {
		
		return $this->getBedrooms()->contains( Plot::STUDIO_LABEL );
		
	}
	
}