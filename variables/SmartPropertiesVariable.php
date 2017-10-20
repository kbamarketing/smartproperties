<?php
	
namespace Craft;

class SmartPropertiesVariable {
	
	public function factory( EntryModel $entry ) {
		
		return craft()->smartProperties->getContainer( $entry );
		
	}
	
}