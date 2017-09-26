<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Article;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class Details extends Path
{
    /**
     * @var string
     */
    protected $_sXPath = "//div[contains(@id, 'details')]";
}