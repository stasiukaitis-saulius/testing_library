<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\Basket;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class Item extends Path
{
    protected $_sXPath = "//tr[contains(@id, 'cartItem_1')]";

    protected $_sCustomXPath = "//tr[contains(@id, 'cartItem_%s')]";
}