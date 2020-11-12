<?php
/**
 * imagecolor plugin for Craft CMS 3.x
 *
 * Extracts the dominant color of an image into a color field.
 *
 * @link      https://niklassonnenschein.de
 * @copyright Copyright (c) 2019 Niklas Sonnenschein
 */

namespace hfg\imagecolor;

use hfg\imagecolor\fields\ImageColorField;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class Imagecolor
 *
 * @author    Niklas Sonnenschein
 * @package   Imagecolor
 * @since     1.0.0
 *
 */
class ImageColor extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var ImageColor
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->_registerFieldTypes();
        $this->_registerCpRoutes();
        $this->_registerSiteRoutes();

        Craft::info(
            Craft::t(
                'imagecolor',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Private Methods
    // =========================================================================

    private function _registerFieldTypes()
    {
        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = ImageColorField::class;
        });
    }

    private function _registerCpRoutes()
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $rules = [
                "imagecolor/image-extractor" => "imagecolor/image-extractor/get-color"
            ];
            $event->rules = array_merge($event->rules, $rules);
        });
    }

    private function _registerSiteRoutes()
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_SITE_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $rules = [
                "imagecolor/image-extractor" => "imagecolor/image-extractor"
            ];
            $event->rules = array_merge($event->rules, $rules);
        });
    }
}
