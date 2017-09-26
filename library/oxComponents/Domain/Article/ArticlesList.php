<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\Article;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Article\ArticlesList as ArticleListPath;

/**
 * Class articleListComponent
 * @package OxidEsales\TestingLibrary\oxComponents\Domain\Article
 */
class ArticlesList extends Component
{
    /**
     * @return ArticleListPath
     */
    public function getPath()
    {
        return new ArticleListPath($this->getIdentity());
    }

    /**
     * @return ArticlesListBoxes
     */
    public function getArticlesListBoxes($identifier = null)
    {
        return new ArticlesListBoxes($this->getNode(), $identifier);
    }
}