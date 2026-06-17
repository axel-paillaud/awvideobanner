<?php
/**
 * @author    Axelweb <contact@axelweb.fr>
 * @copyright 2026 Axelweb
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

declare(strict_types=1);

namespace Axelweb\AwVideoBanner\Controller;

use Axelweb\AwVideoBanner\Helper\VideoHelper;
use Configuration;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminConfigurationController extends FrameworkBundleAdminController
{
    public function index(Request $request): Response
    {
        $formHandler = $this->get('axelweb.awvideobanner.form.general_form_data_handler');
        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $hasError = false;

            $videoFile = $data['video'] ?? null;
            if ($videoFile !== null) {
                $result = VideoHelper::processUpload($videoFile);

                if (isset($result['error'])) {
                    $this->addFlash('error', $result['error']);
                    $hasError = true;
                } else {
                    if (isset($result['warning'])) {
                        $this->addFlash('warning', $result['warning']);
                    }
                    Configuration::updateValue('AWVIDEOBANNER_VIDEO_PATH', $result['filename']);
                }
            }

            Configuration::updateValue('AWVIDEOBANNER_MUTED', !empty($data['muted']) ? '1' : '0');

            if (!$hasError) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
            }

            return $this->redirectToRoute('awvideobanner_form_configuration');
        }

        return $this->render('@Modules/awvideobanner/views/templates/admin/form.html.twig', [
            'generalForm'     => $form->createView(),
            'currentVideoUrl' => VideoHelper::getCurrentVideoUrl(),
        ]);
    }
}
