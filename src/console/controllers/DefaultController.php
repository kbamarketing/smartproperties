<?php
/**
 * Smart Properties plugin for Craft CMS 3.x
 *
 * A plugin to build complex property relationships
 *
 * @link      https://weareaduro.com
 * @copyright Copyright (c) 2020 Aduro
 */

namespace kbamarketing\smartproperties\console\controllers;

use Craft;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Default Command
 *
 * @author    Aduro
 * @package   SmartProperties
 * @since     1.0.2
 */
class DefaultController extends Controller
{
    // Public Methods
    // =========================================================================

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
