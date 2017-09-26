<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Domain\MiniBasket;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class Counter extends Path
{
    /**
     * @var string
     */
    protected $_sXPath = "//span[contains(@class, 'counter')]";
}