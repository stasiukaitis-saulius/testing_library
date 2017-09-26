<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Article;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class ArticlesListBoxes extends Path
{
    /**
     * @var string Component xPath
     */
    protected $_sXPath = "//*[contains(@id, 'searchList')]";

    protected $_sCustomXPath = "//*[contains(@id, '%s')]";
}