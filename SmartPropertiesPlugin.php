<?php
	
namespace Craft;

class SmartPropertiesPlugin extends BasePlugin {
	
	protected $_version = '1.0.0',
		$_schemaVersion = '1.0.0',
		$_name = 'Smart Properties',
		$_url = 'https://github.com/kbamarketing/smart-properties',
		$_releaseFeedUrl = 'https://raw.githubusercontent.com/kbamarketing/smart-properties/master/releases.json',
		$_documentationUrl = 'https://github.com/kbamarketing/smart-properties/blob/master/README.md',
		$_description = '',
		$_developer = 'Terence O\'Donoghue',
		$_developerUrl = 'http://creativelittledots.co.uk',
		$_minVersion = '2.5';
		
	public function init()
    {
	    
	    craft()->on( 'entries.saveEntry', function( Event $event ) {
			    
		    $entry = $event->params['entry'];
		    
		    if( $entry->section->handle == 'developments' ) {
			    			    
			    craft()->smartProperties->cacheProperty( $entry );
			    
			}
		    
		});
		
		craft()->on( 'entries.deleteEntry', function( Event $event ) {
			    
		    $entry = $event->params['entry'];
		    
		    if( $entry->section->handle == 'developments' ) {
		    
		    	craft()->smartProperties->deleteCache( $entry );
		    	
		    }
		    
		});
	    
    }

    public function getName()
    {
        return Craft::t($this->_name);
    }
    
    public function getUrl()
    {
        return $this->_url;
    }
    
    public function getVersion()
    {
        return $this->_version;
    }
    
    public function getDeveloper()
    {
        return $this->_developer;
    }
    
    public function getDeveloperUrl()
    {
        return $this->_developerUrl;
    }
    
    public function getDescription()
    {
        return $this->_description;
    }
    
    public function getDocumentationUrl()
    {
        return $this->_documentationUrl;
    }
    
    public function getSchemaVersion()
    {
        return $this->_schemaVersion;
    }
    
    public function getReleaseFeedUrl()
    {
        return $this->_releaseFeedUrl;
    }
    
    public function getCraftRequiredVersion()
    {
        return $this->_minVersion;
    }
    
    public function hasCpSection()
    {
        return false;
    }

	public function addTwigExtension()
	{
	    Craft::import('plugins.smartproperties.twigextensions.SmartPropertiesTwigExtension');
	
	    return new SmartPropertiesTwigExtension();
	}
	
	protected function defineSettings()
    {
	    return array(
		    'spUseCache' => array(AttributeType::Bool, 'default' => false),
        );
	}
	
	public function getSettingsHtml()
    {
        return craft()->templates->render('smartproperties/settings', array(
            'settings' => $this->getSettings()
        ));
    }
    
    public function registerCachePaths()
    {
        return array(
            craft()->path->getRuntimePath().'smartproperties/' => Craft::t('SmartProperties caches'),
        );
    }

}