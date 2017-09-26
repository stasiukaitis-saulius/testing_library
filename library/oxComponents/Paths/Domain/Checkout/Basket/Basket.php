<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\Basket;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class Basket extends Path
{
    /**
     * @var string
     */
    protected $_sXPath = "//div[contains(@class, 'lineBox')][2]";
}