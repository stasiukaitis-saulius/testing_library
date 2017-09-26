<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Base\Select;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class SelectionElementByName extends Path
{
    protected $_sXPath = "//li/a[text()='']";

    protected $_sCustomXPath = "//li/a[text()='%s']";
}