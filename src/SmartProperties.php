<?php
/**
 * Smart Properties plugin for Craft CMS 3.x
 *
 * A plugin to build complex property relationships
 *
 * @link      https://weareaduro.com
 * @copyright Copyright (c) 2020 Aduro
 */

namespace KBAMarketing\SmartProperties;

use services\SmartPropertiesService as SmartPropertiesServiceService;
use variables\SmartPropertiesVariable;
use twigextensions\SmartPropertiesTwigExtension;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\console\Application as ConsoleApplication;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

/**
 * Class SmartProperties
 *
 * @author    Aduro
 * @package   SmartProperties
 * @since     1.0.2
 *
 * @property  SmartPropertiesServiceService $smartPropertiesService
 */
class SmartProperties extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var SmartProperties
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.2';

    /**
     * @var bool
     */
    public $hasCpSettings = false;

    /**
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Craft::$app->view->registerTwigExtension(new SmartPropertiesTwigExtension());

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'KBAMarketing\SmartProperties\console\controllers';
        }

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('smartProperties', SmartPropertiesVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'smart-properties',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
        
        Event::on( 'entries.saveEntry', function( Event $event ) {
			    
		    $entry = $event->params['entry'];
		    
		    if( $entry->section->handle == 'developments' ) {
			    			    
			    craft()->smartProperties->cacheProperty( $entry );
			    
			}
		    
		});
		
		Event::on( 'entries.deleteEntry', function( Event $event ) {
			    
		    $entry = $event->params['entry'];
		    
		    if( $entry->section->handle == 'developments' ) {
		    
		    	craft()->smartProperties->deleteCache( $entry );
		    	
		    }
		    
		});
    }

    // Protected Methods
    // =========================================================================
    
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
