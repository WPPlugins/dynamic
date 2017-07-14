<?php
/**
 * Created by PhpStorm.
 * User: lubchik
 * Date: 6/2/2017
 * Time: 2:46 AM
 */
include_once dirname(dirname(__FILE__)) .'/Logic/DynamicCMS.php';
class DynamicInternal
{
    public function Manage($Request)
    {
        if (isset($Request['formaction']) && isset($Request['formParameters'])
            && (isset($Request['formParameters']['formName']) || isset($Request['formParameters']['formId']))
        ) {
         //something happens here
        }
    }
}