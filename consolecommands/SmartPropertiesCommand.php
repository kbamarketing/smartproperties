<?php
	
namespace Craft;

class SmartPropertiesCommand extends BaseCommand {
	
	public function actionCache( $entry_id = null ) {
		
		if( $entry_id ) {
			
			$entry = craft()->entries->getEntryById( $entry_id );
			
			$this->cacheProperty( $entry );
			
		} else {
			
			$criteria = craft()->elements->getCriteria(ElementType::Entry);
		
			$criteria->section = 'developments';
			$criteria->limit = null;
			$criteria->level = 1;
			
			$entries = $criteria->find();
			
			foreach($entries as $entry) {
				
				$this->cacheProperty( $entry );
				
			}
			
		}
		
		echo "Cached Properties!\n";
		
	}
	
	protected function cacheProperty( EntryModel $entry ) {
		
		$container = craft()->smartProperties->cacheProperty( $entry, true );
		
		echo "Cached Properties for Entry #{$container->entryId}!\n";
		
	}

}

?>