<?php

namespace App\datalog;

use yii\helpers\ArrayHelper;

class LogAnti extends DataLog
{
    
    public $iSequence = 0;
    
    public $strData = 'NULL';
    
    function rules()
    {
        $rules = [];
        return ArrayHelper::merge(parent::rules(), $rules);
        
    }
    
}