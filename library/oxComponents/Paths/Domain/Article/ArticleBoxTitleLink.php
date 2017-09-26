<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Article;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class ArticleBoxTitleLink extends Path
{
    /**
     * @var string
     */
    protected $_sXPath = "//a[contains(@class, 'title')]";
}