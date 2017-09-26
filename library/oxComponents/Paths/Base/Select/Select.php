<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Base\Select;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class Select extends Path
{
    protected $_sXPath = "//ul[contains(@role, 'menu')]";

    protected $_sCustomXPath = "//ul[contains(@role, 'menu')][%s]";
}