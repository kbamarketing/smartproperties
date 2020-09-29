<?php
/**
 * Smart Properties plugin for Craft CMS 3.x
 *
 * A plugin to build complex property relationships
 *
 * @link      https://weareaduro.com
 * @copyright Copyright (c) 2020 Aduro
 */

namespace kbamarketing\smartproperties;

use kbamarketing\smartproperties\variables\Variable;
use kbamarketing\smartproperties\twigextensions\TwigExtension;
use kbamarketing\smartproperties\models\Settings;

use Craft;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\console\Application as ConsoleApplication;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterCacheOptionsEvent;
use craft\utilities\ClearCaches;

use yii\base\Event;

/**
 * Class smartproperties
 *
 * @author    Aduro
 * @package   smartproperties
 * @since     1.0.2
 *
 * @property  smartpropertiesServiceService $smartPropertiesService
 */
class Plugin extends \craft\base\Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var smartproperties
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
    public $hasCpSettings = true;

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

        Craft::$app->view->registerTwigExtension(new TwigExtension());

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'kbamarketing\smartproperties\console\controllers';
        }

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('smartProperties', Variable::class);
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
                'smartproperties',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
        
        \Craft::$app->on( 'entries.saveEntry', function( Event $event ) {
			    
		    $entry = $event->params['entry'];
		    
		    if( $entry->section->handle == 'developments' ) {
			    			    
			    craft()->smartProperties->cacheProperty( $entry );
			    
			}
		    
		});
		
		\Craft::$app->on( 'entries.deleteEntry', function( Event $event ) {
			    
		    $entry = $event->params['entry'];
		    
		    if( $entry->section->handle == 'developments' ) {
		    
		    	craft()->smartProperties->deleteCache( $entry );
		    	
		    }
		    
		});
		
		Event::on(
		    ClearCaches::class,
		    ClearCaches::EVENT_REGISTER_CACHE_OPTIONS,
		    function(RegisterCacheOptionsEvent $event) {
		        $event->options[] = [
		            'key' => 'smartproperties',
		            'label' => \Craft::t('plugin-handle', 'SmartProperties caches'),
		            'action' => \Craft::$app->path->getStoragePath().'/smartproperties'
		        ];
		    }
		);
    }

    // Protected Methods
    // =========================================================================
	
	protected function createSettingsModel()
    {
        return new Settings();
    }
    
    protected function settingsHtml()
    {
        return \Craft::$app->getView()->renderTemplate(
            'smartproperties/settings',
            [ 'settings' => $this->getSettings() ]
        );
    }

}
