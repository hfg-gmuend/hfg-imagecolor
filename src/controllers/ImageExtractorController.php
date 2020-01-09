<?php
/**
 * imagecolor plugin for Craft CMS 3.x
 *
 * Extracts the dominant color of an image into a color field.
 *
 * @link      https://niklassonnenschein.de
 * @copyright Copyright (c) 2019 Niklas Sonnenschein
 */

namespace hfg\imagecolor\controllers;

use hfg\imagecolor\ImageColor;

use aelvan\imager\Imager;

use Craft;
use craft\helpers\Json;
use craft\web\Controller;
use yii\web\Response;

/**
 * @author    Niklas Sonnenschein
 * @package   Imagecolor
 * @since     1.0.0
 */
class ImageExtractorController extends Controller
{
    private $heroDimensions = array(
        "width" => 1200,
        "height" => 540
    );
    private $colorCount = 8;
    private $colorMode = "hex";
    private $quality = 10;

    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function actionGetColor(): Response
    {
        $this->requireAcceptsJson();

        $assets = Craft::$app->getAssets();
        $request = Craft::$app->getRequest();

        $assetId = $request->getParam("assetId");
        $image = $assets->getAssetById($assetId);

        $transformedImage = Imager::$plugin->imager->transformImage($image, $this->heroDimensions);
        $color = Imager::$plugin->color->getDominantColor($transformedImage, $this->quality, $this->colorMode);
        $palette = Imager::$plugin->color->getColorPalette($transformedImage, $this->colorCount, $this->quality, $this->colorMode);

        return $this->asJson(
            [
                "color" => $color,
                "palette" => $palette,
            ]
        );
    }
}