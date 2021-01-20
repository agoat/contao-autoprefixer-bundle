<?php

/*
 * Autoprefixer plugin for Contao Open Source CMS.
 *
 * @copyright  Arne Stappen (alias aGoat) 2021
 * @package    contao-autoprefixer
 * @author     Arne Stappen <mehh@agoat.xyz>
 * @link       https://agoat.xyz
 * @license    LGPL-3.0
 */

namespace Agoat\AutoPrefixerBundle\ContaoManager;

use Agoat\AutoPrefixerBundle\AgoatAutoPrefixerBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;


/**
 * Plugin for the Contao Manager.
 *
 * @return BundleConfig
 */
class Plugin implements BundlePluginInterface
{

    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(AgoatAutoPrefixerBundle::class)
                        ->setLoadAfter([ContaoCoreBundle::class])
                        ->setReplace(['autoprefixer']),
        ];
    }

}
