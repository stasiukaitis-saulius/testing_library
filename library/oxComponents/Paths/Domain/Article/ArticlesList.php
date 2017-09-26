<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Article;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class ArticlesList extends Path
{
    /**
     * @var string Component xPath
     */
    protected $_sXPath = "//div[contains(@id, 'content')]";

    protected $_sCustomXPath = "//div[contains(@id, '%s')]";
}