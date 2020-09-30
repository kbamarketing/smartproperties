<?php
/**
 * Smart Properties plugin for Craft CMS 3.x
 *
 * A plugin to build complex property relationships
 *
 * @link      https://weareaduro.com
 * @copyright Copyright (c) 2020 Aduro
 */

namespace kbamarketing\smartproperties\services;

use Craft;
use craft\base\Component;
use craft\elements\Entry as EntryModel;

use kbamarketing\smartproperties\models\SmartProperties_ContainerModel as Container;
use kbamarketing\smartproperties\models\SmartProperties_PhasedModel as Phased;
use kbamarketing\smartproperties\models\SmartProperties_PhaseModel as Phase;

use kbamarketing\smartproperties\Plugin;

/**
 * @author    Aduro
 * @package   SmartProperties
 * @since     1.0.2
 */
class Service extends Component
{
    protected function useCache()
	{
		$settings = Plugin::getInstance()->getSettings();
		return $settings->spUseCache;
	}
	
	public function getContainer( EntryModel $entry, $use_cache = null ) {
		
		$entry = $entry->getParent() ? $entry->getParent() : $entry;
		
		if( ( ! is_null( $use_cache ) ? $use_cache : $this->useCache() ) && ( $container = $this->getCache($entry) ) && 0 ) {
			
			return $container;
			
		} else {
		
			$container = $this->compileContainer( $entry );
			
			if( ! is_null( $use_cache ) ? $use_cache : $this->useCache() ) {
				
				$this->setCache($entry, $container);
				
			}
			
			return $container;
			
		}
		
	}
	
	protected function compileContainer( EntryModel $entry ) {
		
		switch( $entry->type->name ) {
				
			case 'Phased' :
			
				$container = Phased::compile( $entry )->flatten();
				
			break;
			
			default :
			
				$container = Phase::compile( $entry )->flatten();
			
			break;
			
		}
		
		return $container;
		
	}
	
	protected function buildContainer( array $data ) {
		
		switch( !empty( $data['isPhased'] ) ) {
				
			case true :
			
				$container = new Phased( $data );
				
			break;
			
			default :
			
				$container = new Phase( $data );
			
			break;
			
		}
		
		return $container;
		
	}
	
	public function cacheProperty( EntryModel $entry, $force = false ) {
		
		if( $this->useCache() || $force ) {
			
			$entry = $entry->getParent() ? $entry->getParent() : $entry;
		
			$container = $this->compileContainer( $entry );
			
			$this->setCache($entry, $container);
			
			return $this->buildContainer( $container );
			
		}
		
	}
	
	public function getCache(EntryModel $entry)
    {
   
        $cacheKey = $this->getCacheKey($entry);

        return Craft::$app->cache->get($cacheKey);

    }

	public function setCache(EntryModel $entry, $data, $expire = null, $dependency = null) {

        $cacheKey = $this->getCacheKey($entry);

        return Craft::$app->cache->set($cacheKey, $data, $expire, $dependency);

    }
    
    public function deleteCache(EntryModel $entry) {

        $cacheKey = $this->getCacheKey($entry);

        return Craft::$app->cache->deleteCachesByKey($cacheKey);

    }

	private function getCacheKey(EntryModel $entry)
    {

        return 'smartproperties.' . md5(serialize([
	        'smartproperties',
	        'entry_id',
	        $entry->id
        ]));
    }

}
