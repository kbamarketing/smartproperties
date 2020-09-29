<?php

namespace KBAMarketing\SmartProperties\models;

use KBAMarketing\SmartProperties\SmartProperties;

use Craft;

use SmartProperties_PlotModel as Plot;
use SmartProperties_CollectionModel as Collection;

trait SmartProperties_HasPlotsModel {
	
	protected function getPlots() {
		
		$plots = $this->getProperty('plots');
		
		return $plots ? $plots : new Collection();
		
	}
	
	protected function getPrices() {
		
		return $this->getPlots()->pluck( 'price' );
		
	}
	
	protected function hasVariatingPrices() {
		
		return $this->getMinPrice() !== $this->getMaxPrice();
		
	}
	
	protected function getAvailablePlots() {
		
		$plots = $this->getProperty('availablePlots');
		
		return $plots ? $plots : $this->getPlots()->filter(function(Plot $plot) {
				
			return $plot->getAttribute('isAvailable');
			
		} );
		
	}
	
	protected function getFilteredPrices() {
		
		return array_filter( $this->getPrices()->all() );
		
	}
	
	protected function hasPrices() {
		
		return $this->getFilteredPrices() ? true : false;
		
	}
	
	protected function isAvailable() {
		
		return $this->hasPrices() ? true : false;
		
	}
	
	protected function getMinPrice() {
		
		$prices = $this->getFilteredPrices();
		
		return $prices ? min( $prices ) : 0;
		
	}
	
	protected function getMaxPrice() {
		
		$prices = $this->getFilteredPrices();
		
		return $prices ? max( $prices ) : 0;
		
	}
	
	protected function getFormattedMinPrice() {
		
		return $this->getMinPrice() ? $this->formatCurrency( $this->getMinPrice() ) : null;
		
	}
	
	public function getPriceHtml() {
		
		return $this->getFormattedMinPrice();
		
	}
	
	protected function getAvailabilityHtml() {

		if (! count( $this->getPlots())) {

			return Plot::TO_BE_RELEASED_LABEL;

		} elseif( ! count( $this->getPlots()->filter(function(Plot $plot) {

			return ! $plot->is(Plot::SOLD_LABEL);
			
		} ) ) ) {
			
			return Plot::ALL_SOLD_LABEL;
			
		} elseif( ! count( $this->getPlots()->filter(function(Plot $plot) {
			
			return ! $plot->is(Plot::RESERVED_LABEL) && ! $plot->is(Plot::SOLD_LABEL);
			
		} ) ) ) {
			
			return Plot::ALL_RESERVED_LABEL;
			
		} else {
			
			return Plot::TO_BE_RELEASED_LABEL;
			
		}
		
	}
	
	protected function getAvailablePlotsHtml() {
		
		if( ! count( $this->getPlots()->filter(function(Plot $plot) {
			
			return ! $plot->is(Plot::SOLD_LABEL);
			
		} ) ) ) {
			
			return Plot::ALL_SOLD_LABEL;
			
		} elseif( ! count( $this->getPlots()->filter(function(Plot $plot) {
			
			return ! $plot->is(Plot::RESERVED_LABEL) && ! $plot->is(Plot::SOLD_LABEL);
			
		} ) ) ) {
			
			return Plot::ALL_RESERVED_LABEL;
			
		} else {
			
			return $this->getAvailablePlots()->count() . ' ' . Plot::AVAILABLE_LABEL;
			
		}
		
	}
	
}