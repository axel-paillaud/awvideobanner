<?php
/**
 * @author    Axelweb <contact@axelweb.fr>
 * @copyright 2007-2024 Axelweb
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

declare(strict_types=1);

namespace Axelweb\AwVideoBanner\Form;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class GeneralFormType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('video', FileType::class, [
                'label' => $this->trans('Video file', 'Modules.Awvideobanner.Admin'),
                'help' => $this->trans('MP4 or WebM only, max 200 MB. Replaces the current video.', 'Modules.Awvideobanner.Admin'),
                'required' => false,
                'attr' => ['accept' => 'video/mp4,video/webm'],
            ])
            ->add('muted', CheckboxType::class, [
                'label' => $this->trans('Mute video (no sound)', 'Modules.Awvideobanner.Admin'),
                'required' => false,
            ]);
    }
}
