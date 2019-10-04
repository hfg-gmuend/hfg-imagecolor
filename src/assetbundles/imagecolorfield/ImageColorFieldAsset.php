<?php
/**
 * imagecolor plugin for Craft CMS 3.x
 *
 * Extracts the dominant color of an image into a color field.
 *
 * @link      https://niklassonnenschein.de
 * @copyright Copyright (c) 2019 Niklas Sonnenschein
 */

namespace hfg\imagecolor\assetbundles\imagecolorfield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Niklas Sonnenschein
 * @package   Imagecolor
 * @since     1.0.0
 */
class ImageColorFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@hfg/imagecolor/assetbundles/imagecolorfield/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/ImageColor.js',
        ];

        $this->css = [
            'css/ImageColor.css',
        ];

        parent::init();
    }
}
