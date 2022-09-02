<?php

namespace app\assets;

use yii\web\AssetBundle;

class DropzoneAsset extends AssetBundle
{
    public $basePath = '@webroot/vendor/dropzone';
    public $baseUrl = '@web/vendor/dropzone';

    public $css = [
        '/css/dropzone.min.css',
    ];
    public $js = [
        '/js/dropzone.min.js'
    ];
}
