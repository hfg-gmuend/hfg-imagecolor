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
use craft\errors\SiteNotFoundException;
use craft\helpers\Html;
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
    /**
     * @var string|null The UID of the site that this field should relate elements from
     */
    public $targetSiteId;

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
            $hexColor = substr($value->color, 0, 1) == "#" ? $value->color : "#" . $value->color;
            $serialized = [
                "image" => $value->image,
                "color" => $value->color,
                "brightness" => Imager::$plugin->color->getPercievedBrightness($hexColor)
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
        $id = Html::id($this->handle);
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
                "siteId" => $this->targetSiteId($element),
            ),
            "fieldId" => $this->id,
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

    /**
     * Returns the site ID that target elements should have.
     *
     * @param ElementInterface|null $element
     * @return int
     */
    protected function targetSiteId(ElementInterface $element = null): int
    {
        if (Craft::$app->getIsMultiSite()) {
            if ($this->targetSiteId) {
                try {
                    return Craft::$app->getSites()->getSiteByUid($this->targetSiteId)->id;
                } catch (SiteNotFoundException $exception) {
                    Craft::warning($exception->getMessage(), __METHOD__);
                }
            }

            if ($element !== null) {
                return $element->siteId;
            }
        }

        return Craft::$app->getSites()->getCurrentSite()->id;
    }
}
