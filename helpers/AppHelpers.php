<?php


namespace app\helpers;


class AppHelpers
{

    public static function getServiceTypes() {
        return [
            1 => 'Internal Link',
            2 => 'External Link',
            3 => 'CMS',
            4 => 'No Link'
        ];
    }

}
