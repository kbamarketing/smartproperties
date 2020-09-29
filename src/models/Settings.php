<?php

namespace KBAMarketing\SmartProperties\models;

use craft\base\Model;

class Settings extends Model
{
    public $spUseCache = false;

    public function rules()
    {
        return [
            [['spUseCache']]
            // ...
        ];
    }
}