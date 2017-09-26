<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Base;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class Link extends Path
{
    /**
     * @var string
     */
    protected $_sXPath = "//a";

    protected $_sCustomXPath = "//a[contains(@id, '%s')]";

}