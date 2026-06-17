<?php
/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Axelweb <contact@axelweb.fr>
 * @copyright 2007-2024 Axelweb
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Axelweb
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use Axelweb\AwVideoBanner\Helper\VideoHelper;

class AwVideoBanner extends Module
{
    public function __construct()
    {
        $this->name = 'awvideobanner';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Axelweb';
        $this->need_instance = 1;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Video Banner', [], 'Modules.Awvideobanner.Admin');
        $this->description = $this->trans('Upload and display a looping video banner on the front-office', [], 'Modules.Awvideobanner.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall this module?', [], 'Modules.Awvideobanner.Admin');

        $this->ps_versions_compliancy = [
            'min' => '1.7.8',
            'max' => _PS_VERSION_,
        ];
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    public function install(): bool
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        VideoHelper::ensureUploadDir();

        $installed = parent::install()
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->registerHook('displayHome')
            && Configuration::updateValue('AWVIDEOBANNER_VIDEO_PATH', '')
            && Configuration::updateValue('AWVIDEOBANNER_MUTED', '1');

        // Prevent 'Unable to generate a URL for the named route [...]' error,
        // clear Symfony cache
        if ($installed) {
            Tools::clearSf2Cache();
        }

        return $installed;
    }

    public function uninstall(): bool
    {
        return parent::uninstall()
            && Configuration::deleteByName('AWVIDEOBANNER_VIDEO_PATH')
            && Configuration::deleteByName('AWVIDEOBANNER_MUTED');
    }

    /**
     * Redirect to the module symfony configuration page
     *
     * @return void
     */
    public function getContent(): void
    {
        $route = $this->get('router')->generate('awvideobanner_form_configuration');
        Tools::redirectAdmin($route);
    }

    public function hookDisplayHome()
    {
        $videoFilename = Configuration::get('AWVIDEOBANNER_VIDEO_PATH');

        if (empty($videoFilename)) {
            return '';
        }

        $ext = pathinfo($videoFilename, PATHINFO_EXTENSION);

        $this->context->smarty->assign([
            'awvideobanner_video_url'  => VideoHelper::getUploadUrl() . $videoFilename,
            'awvideobanner_video_type' => $ext === 'webm' ? 'video/webm' : 'video/mp4',
            'awvideobanner_muted'      => (bool) Configuration::get('AWVIDEOBANNER_MUTED'),
        ]);

        return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
    }

    /**
     * Hook to register CSS and JS on front-office pages
     */
    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'module-awvideobanner-style',
            'modules/' . $this->name . '/views/css/awvideobanner.css',
            [
                'media' => 'all',
                'priority' => 200,
            ]
        );

    }
}
