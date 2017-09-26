<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\Article;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Article\ArticlesListBoxes as ArticlesListBoxesPath;

class ArticlesListBoxes extends Component
{
    /**
     * @return mixed|ArticlesListBoxes
     */
    public function getPath()
    {
        return new ArticlesListBoxesPath($this->getIdentity());
    }

    /**
     * @param $iBoxId
     * @return ArticleBox
     */
    public function getArticleBox($iBoxId)
    {
        return new ArticleBox($this->getNode(), $iBoxId);
    }
}