<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Base;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class Button extends Path
{
    protected $_sXPath = "//button";

    protected $_sCustomXPath = "//button[contains(@class, '%s')]";
}