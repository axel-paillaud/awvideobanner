# AwVideoBanner

A PrestaShop 1.7.8+ module to upload and display a looping video banner on the front-office, by [Axelweb](https://axelweb.fr).

## Requirements

- PrestaShop 1.7.8+
- PHP 7.4+
- Composer

## Installation

1. Run `composer install` in the module directory.
2. Install the module via the PrestaShop back-office, or with the CLI:
   ```
   php bin/console prestashop:module install awvideobanner
   ```
3. Register the `displayHome` hook if not done automatically:
   ```
   php bin/console prestashop:module hooks awvideobanner
   ```

## Configuration

Go to **Modules > Module Manager**, find "Video Banner" and click **Configure**.

- **Video file** - upload an MP4 or WebM file (max 200 MB). The file is stored in `img/video/awvideobanner/` and named `banner.mp4` or `banner.webm`. Uploading a new file replaces the previous one.
- **Mute video** - when checked, the video autoplays silently in a loop. When unchecked, the video is displayed with native player controls (autoplay is blocked by browsers when sound is enabled).

## Project structure

```
awvideobanner/
├── awvideobanner.php
├── composer.json
├── config/
│   ├── routes.yml
│   └── services.yml
├── src/
│   ├── Controller/
│   │   └── AdminConfigurationController.php
│   ├── Form/
│   │   ├── GeneralDataConfiguration.php
│   │   ├── GeneralFormDataProvider.php
│   │   └── GeneralFormType.php
│   └── Helper/
│       └── VideoHelper.php
├── translations/
│   └── fr-FR/
│       └── ModulesAwvideobannerAdmin.fr-FR.xlf
├── vendor/
└── views/
    ├── css/
    │   ├── admin/form.css
    │   └── awvideobanner.css
    └── templates/
        ├── admin/
        │   └── form.html.twig
        └── hook/
            └── displayHome.tpl
```

## License

[Academic Free License 3.0 (AFL-3.0)](https://opensource.org/licenses/AFL-3.0)
