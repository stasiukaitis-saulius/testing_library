<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Base\Select;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class SelectionElement extends Path
{
    protected $_sXPath = "//ul/li[1]/a";

    protected $_sCustomXPath = "//ul/li[%s]/a";
}