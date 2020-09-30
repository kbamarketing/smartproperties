<?php
	
namespace kbamarketing\smartproperties\models;

use Craft;

use Illuminate\Support\Collection;

class SmartProperties_CollectionModel extends Collection {
	
	public function length() {
		
		return $this->count();
		
	}
	
	public function setAttribute($key, $value) {
		
		$this[$key] = $value;
		
	}
	
	public function getAttribute($key) {
		
		return $this->get($key);
		
	}
	
	public function concat( $property = null, $delimiter = ', ', $final = ' & ' ) {
		
		$parts = $property ? $this->pluck($property)->all() : $this->all();
		
		if( count( $parts ) > 1 ) {
		
			$last = array_pop($parts);
			
			return implode( $final, [ implode( $delimiter, $parts ), $last ] );
			
		}
			
		return implode( ',', $parts );
		
	}
	
	public function find( $id ) {
		
		$collection = $this->keyBy('id');
		
		return $collection->get( $id );
		
	}
	
	public function whereAll($args) {
		
		$collection = $this;
		
		foreach($args as $key => $arg) {
			
			$collection = $collection->where($key, $arg);
			
		}
		
		return $collection;
		
	}
	
}
