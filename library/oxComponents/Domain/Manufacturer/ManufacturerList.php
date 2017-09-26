<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\Manufacturer;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Manufacturer\ManufacturerList as ManufacturerListPath;

/**
 * Class ManufacturerListComponent
 * @package OxidEsales\TestingLibrary\oxComponents\Domain\Manufacturer
 */
class ManufacturerList extends Component
{
    /**
     * @return ManufacturerListPath
     */
    public function getPath()
    {
        return new ManufacturerListPath($this->getIdentity());
    }

}