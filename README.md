# Image + Color

Select an image in the asset field and automatically get the image's dominant color in a color field. 

## Requirements

This plugin requires Craft CMS 3.3.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require hfg/imagecolor

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for imagecolor.

## Usage

```twig
{{ fieldName.asset.getUrl() }}
{{ fieldName.color }}
{{ fieldName.brightness }}
```