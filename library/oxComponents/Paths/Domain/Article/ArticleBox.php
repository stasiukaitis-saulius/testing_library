<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Article;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class ArticleBox extends Path
{
    protected $_sXPath = "/div/div[1]";

    protected $_sCustomXPath = "/div/div[%s]";
}