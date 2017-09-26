<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Manufacturer;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class ManufacturerList extends Path
{
    /**
     * @var string Component xPath
     */
    protected $_sXPath = "//div[contains(@id, 'manufacturerSlider')]";
}