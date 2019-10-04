<?php
/**
 * imagecolor plugin for Craft CMS 3.x
 *
 * Extracts the dominant color of an image into a color field.
 *
 * @link      https://niklassonnenschein.de
 * @copyright Copyright (c) 2019 Niklas Sonnenschein
 */

namespace hfg\imagecolor\models;

use hfg\imagecolor\ImageColor;

use Craft;
use craft\base\Model;

/**
 * @author    Niklas Sonnenschein
 * @package   Imagecolor
 * @since     1.0.0
 */
class ImageColorModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $image = "";
    public $asset = "";
    public $color = "#f5f5f5";
    public $brightness = 0;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
        
        $this->asset = $this->getAsset();
    }

    public function getAsset()
    {
        $id = is_array($this->image) ? $this->image[0] : false;
        if ($id && $asset = Craft::$app->getAssets()->getAssetById($id)) {
            return $asset;
        }

        return null;
    }
}
