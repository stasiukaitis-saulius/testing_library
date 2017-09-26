<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\Basket;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class ItemPart extends Path
{
    protected $_sXPath = "//td[1]";

    protected $_sCustomXPath = "//td[%s]";
}