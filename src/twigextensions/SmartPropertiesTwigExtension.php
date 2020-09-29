<?php
/**
 * Smart Properties plugin for Craft CMS 3.x
 *
 * A plugin to build complex property relationships
 *
 * @link      https://weareaduro.com
 * @copyright Copyright (c) 2020 Aduro
 */

namespace KBAMarketing\SmartProperties\twigextensions;

use KBAMarketing\SmartProperties\SmartProperties;

use Craft;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use KBAMarketing\SmartProperties\models\SmartProperties_CollectionModel as Collection;

/**
 * @author    Aduro
 * @package   SmartProperties
 * @since     1.0.2
 */
class SmartPropertiesTwigExtension extends AbstractExtension
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'SmartProperties';
    }

   public function getFilters()
    {
        return array(
            new TwigFilter('concat', [$this, 'concat']),
            new TwigFilter('cut', [$this, 'cut']),
            new TwigFilter('find', [$this, 'find']),
        );
    }
    
    public function concat($collection, $property = null)
    {
	    $collection = $this->makeCollection($collection);
        return $collection->concat($property);
    }
    
    public function find($collection, $id) 
    {
	    $collection = $this->makeCollection($collection);
        return $collection->find($id);
    }
    
    public function cut($collection, $property, $value = null)
    {
	    $args = is_array($property) ? $property : [$property => $value];
	    $collection = $this->makeCollection($collection);
        return $collection->whereAll($args);
    }
	
	public function unique($collection)
    {
	    $collection = $this->makeCollection($collection);
        return $collection->unique();
    }
    
    protected function makeCollection($collection)
    {
	    return $collection instanceof Collection ? $collection : new Collection($collection);
    }

}
