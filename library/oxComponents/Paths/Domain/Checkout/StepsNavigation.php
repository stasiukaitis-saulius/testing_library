<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class StepsNavigation extends Path
{
    /**
     * @var string
     */
    protected $_sXPath = "//ul[contains(@class, 'checkoutSteps')]";
}