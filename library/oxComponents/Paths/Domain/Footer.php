<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Domain;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class Footer extends Path
{
    /**
     * @var string
     */
    protected $_sXPath = "//footer[contains(@id, 'footer')]";
}