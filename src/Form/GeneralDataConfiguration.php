<?php
/**
 * @author    Axelweb <contact@axelweb.fr>
 * @copyright 2026 Axelweb
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

declare(strict_types=1);

namespace Axelweb\AwVideoBanner\Form;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

final class GeneralDataConfiguration implements DataConfigurationInterface
{
    public const AWVIDEOBANNER_MUTED = 'AWVIDEOBANNER_MUTED';

    /** @var ConfigurationInterface */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(): array
    {
        return [
            'muted' => (bool) $this->configuration->get(static::AWVIDEOBANNER_MUTED),
        ];
    }

    public function updateConfiguration(array $configuration): array
    {
        return [];
    }

    public function validateConfiguration(array $configuration): bool
    {
        return true;
    }
}
