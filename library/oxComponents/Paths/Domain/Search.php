<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Domain;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class Search extends Path
{
    /**
     * @var string
     */
    protected $_sXPath = "//div[contains(@class, 'searchBox')]";
}