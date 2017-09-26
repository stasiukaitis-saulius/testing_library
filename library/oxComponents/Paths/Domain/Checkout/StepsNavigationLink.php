<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class StepsNavigationLink extends Path
{
    protected $_sXPath = "//li[contains(@class, 'step1')]";

    protected $_sCustomXPath = "//li[contains(@class, 'step%s')]";
}