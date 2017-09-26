<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Base\Select;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class SelectionSelectedElement extends Path
{
    /**
     * @var string
     */
    protected $_sXPath = "//li[contains(@class, 'active')]";
}