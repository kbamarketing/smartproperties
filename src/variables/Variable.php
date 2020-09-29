<?php
/**
 * Smart Properties plugin for Craft CMS 3.x
 *
 * A plugin to build complex property relationships
 *
 * @link      https://weareaduro.com
 * @copyright Copyright (c) 2020 Aduro
 */

namespace kbamarketing\smartproperties\variables;

use craft\elements\Entry as EntryModel;
use kbamarketing\smartproperties\Plugin;

use Craft;

/**
 * @author    Aduro
 * @package   SmartProperties
 * @since     1.0.2
 */
class Variable
{
    public function factory( EntryModel $entry ) {
		
		return Plugin::getInstance()->service->getContainer( $entry );
		
	}

}
