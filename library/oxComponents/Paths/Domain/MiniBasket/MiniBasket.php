<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Domain\MiniBasket;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class MiniBasket extends Path
{
    /**
     * @var string
     */
    protected $_sXPath = "//div[contains(@id, 'minibasket_container')]";
}