<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\Checkout;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Base\Link;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\StepsNavigation as StepsNavigationPath;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\StepsNavigationLink as StepsNavigationLinkPath;

class StepsNavigation extends Component
{

    /**
     * @return object|StepsNavigationPath
     */
    public function getPath()
    {
        return new StepsNavigationPath();
    }

    public function clickStep($iStepNumber)
    {
        $oStepNode = $this->findNode(new StepsNavigationLinkPath($iStepNumber));

        return new Link($oStepNode);
    }
}