<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Base;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class Input extends Path
{
    protected $_sXPath = "//input[1]";

    protected $_sCustomXPath = "//input[contains(@name, '%s')]";
}