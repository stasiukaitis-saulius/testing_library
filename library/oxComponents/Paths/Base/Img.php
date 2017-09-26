<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Base;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class Img extends Path
{
    protected $_sXPath = "//img[1]";

    protected $_sCustomXPath = "//*[contains(@id, '%s')]";
}