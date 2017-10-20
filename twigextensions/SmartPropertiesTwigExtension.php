<?php
	
namespace Craft;

use Twig_Extension;
use Twig_Filter_Method;
use Craft\SmartProperties_CollectionModel as Collection;

class SmartPropertiesTwigExtension extends Twig_Extension
{
	
	public function getName()
    {
        return 'SmartProperties';
    }


	public function getFilters()
    {
        return array(
            'concat' => new Twig_Filter_Method($this, 'concat'),
            'cut' => new Twig_Filter_Method($this, 'cut'),
            'find' => new Twig_Filter_Method($this, 'find'),
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
	    $collection = $this->makeCollection($collection);
        return $collection->filter(function($item) use($property, $value) {
	        return ( $value && $item[$property] == $value ) || ( ! $value && $item[$property] );
        });
    }
    
    protected function makeCollection($collection)
    {
	    return $collection instanceof Collection ? $collection : new Collection($collection);
    }
    
}