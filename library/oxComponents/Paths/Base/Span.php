<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Base;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class Span extends Path
{
    protected $_sXPath = "//span";

    protected $_sCustomXPath = "//span[contains(@class, '%s')]";
}