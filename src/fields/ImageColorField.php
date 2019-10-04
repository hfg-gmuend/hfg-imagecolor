<?php
/**
 * imagecolor plugin for Craft CMS 3.x
 *
 * Extracts the dominant color of an image into a color field.
 *
 * @link      https://niklassonnenschein.de
 * @copyright Copyright (c) 2019 Niklas Sonnenschein
 */

namespace hfg\imagecolor\fields;

use hfg\imagecolor\ImageColor;
use hfg\imagecolor\assetbundles\imagecolorfield\ImageColorFieldAsset;
use hfg\imagecolor\models\ImageColorModel;

use aelvan\imager\Imager;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\elements\Asset as CraftAsset;
use craft\helpers\Db;
use craft\helpers\Json as JsonHelper;
use yii\db\Schema;

/**
 * @author    Niklas Sonnenschein
 * @package   Imagecolor
 * @since     1.0.0
 */
class ImageColorField extends Field
{
    // Public Properties
    // =========================================================================


    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('imagecolor', 'ImageColor');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if ($value instanceof ImageColorModel) {
            return $value;
        }

        if (is_string($value)) {
            $value = JsonHelper::decodeIfJson($value);
        }

        return new ImageColorModel($value);
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        $serialized = [];
        if ($value instanceof ImageColorModel) {
            $serialized = [
                "image" => $value->image,
                "color" => $value->color,
                "brightness" => Imager::$plugin->color->getPercievedBrightness($value->color)
            ];
        }

        return parent::serializeValue($serialized, $element);
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $view = Craft::$app->getView();

        // Register our asset bundle
        $view->registerAssetBundle(ImageColorFieldAsset::class);

        // Get our id and namespace
        $id = $view->formatInputId($this->handle);
        $namespacedId = $view->namespaceInputId($id);

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = JsonHelper::encode([
            'id' => $namespacedId,
            'name' => $this->handle
        ]);
        //Craft::$app->getView()->registerJs("$('#{$namespacedId}-field').ImagecolorImageColor(" . $jsonVars . ");");
        $view->registerJs("new ImageColor.Field('".$jsonVars."')");

        $elements = array();
        
        if (isset($value->image[0])) {
            $elements[] = Craft::$app->getAssets()->getAssetById((int) $value["image"][0]);
        }

        // Element Select Options
        $elementSelectSettings = array(
            "elementType" => CraftAsset::class,
            "elements" => $elements,
            "limit" => 1,
            "criteria" => array(
                "status" => null
            ),
            "storageKey" => "field." . $this->id,
            "viewMode" => "large"
        );

        // Render the input template
        return $view->renderTemplate(
            'imagecolor/_components/fields/ImageColor_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
                'elementSelectSettings' => $elementSelectSettings
            ]
        );
    }
}
