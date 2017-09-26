<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Footer as FooterPath;

class Footer extends Component
{
    /**
     * @return object|FooterPath
     */
    public function getPath()
    {
        return new FooterPath();
    }

    public function clickManufacturerLink($manufacturerName)
    {
        $this->getNode()->clickLink($manufacturerName);
    }
}