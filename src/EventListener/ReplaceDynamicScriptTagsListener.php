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

namespace Agoat\AutoPrefixerBundle\EventListener;

use Agoat\AutoPrefixerBundle\Contao\AutoCombiner;
use Contao\Controller;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\LayoutModel;
use Contao\StringUtil;
use Contao\Template;

/**
 * @Hook("replaceDynamicScriptTags")
 */
class ReplaceDynamicScriptTagsListener
{

    public function __invoke(string $buffer): string
    {
        // PageModel needed
        if (!isset($GLOBALS['objPage'])) {
            return $buffer;
        }

        $layout = LayoutModel::findByPk($GLOBALS['objPage']->layoutId);

        if (!$layout->autoprefix || $layout === null) {
            return $buffer;
        }

        $combineScripts = $layout->combineScripts;

        $combiner = new AutoCombiner($layout);
        $stylesheetTags = '';

        // Add the CSS framework style sheets
        if (!empty($GLOBALS['TL_FRAMEWORK_CSS']) && \is_array($GLOBALS['TL_FRAMEWORK_CSS'])) {
            foreach (array_unique($GLOBALS['TL_FRAMEWORK_CSS']) as $stylesheet) {
                $combiner->add($stylesheet);
            }
        }

        // Add the internal style sheets
        if (!empty($GLOBALS['TL_CSS']) && \is_array($GLOBALS['TL_CSS'])) {
            foreach (array_unique($GLOBALS['TL_CSS']) as $stylesheet) {
                $options = StringUtil::resolveFlaggedUrl($stylesheet);

                if ($options->static) {
                    $combiner->add($stylesheet, $options->mtime, $options->media);
                } else {
                    $stylesheetTags .= Template::generateStyleTag(
                        Controller::addAssetsUrlTo($stylesheet),
                        $options->media,
                        $options->mtime
                    );
                }
            }
        }

        // Add the user style sheets
        if (!empty($GLOBALS['TL_USER_CSS']) && \is_array($GLOBALS['TL_USER_CSS'])) {
            foreach (array_unique($GLOBALS['TL_USER_CSS']) as $stylesheet) {
                $options = StringUtil::resolveFlaggedUrl($stylesheet);

                if ($options->static) {
                    $combiner->add($stylesheet, $options->mtime, $options->media);
                } else {
                    $stylesheetTags .= Template::generateStyleTag(
                        Controller::addAssetsUrlTo($stylesheet),
                        $options->media,
                        $options->mtime
                    );
                }
            }
        }

        // Create the aggregated style sheet
        if ($combiner->hasEntries()) {
            // Rewrite css with Autoprefixer
            $combiner->addPrefixes();

            if ($combineScripts) {
                $stylesheetTags .= Template::generateStyleTag($combiner->getCombinedFile(), 'all');
            } else {
                foreach ($combiner->getFileUrls() as $strUrl) {
                    $options = StringUtil::resolveFlaggedUrl($strUrl);
                    $stylesheetTags .= Template::generateStyleTag($strUrl, $options->media, $options->mtime);
                }
            }
        }

        // Reset CSS globals
        $GLOBALS['TL_FRAMEWORK_CSS'] = false;
        $GLOBALS['TL_CSS'] = false;
        $GLOBALS['TL_USER_CSS'] = false;

        return str_replace('[[TL_CSS]]', $stylesheetTags, $buffer);
    }

}
