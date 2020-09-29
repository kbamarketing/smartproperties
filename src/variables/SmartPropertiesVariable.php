<?php
/**
 * Smart Properties plugin for Craft CMS 3.x
 *
 * A plugin to build complex property relationships
 *
 * @link      https://weareaduro.com
 * @copyright Copyright (c) 2020 Aduro
 */

namespace KBAMarketing\SmartProperties\variables;

use KBAMarketing\SmartProperties\SmartProperties;

use Craft;

/**
 * @author    Aduro
 * @package   SmartProperties
 * @since     1.0.2
 */
class SmartPropertiesVariable
{
    public function factory( EntryModel $entry ) {
		
		return craft()->smartProperties->getContainer( $entry );
		
	}

}
