<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\Article;

use OxidEsales\TestingLibrary\oxComponents\Base\Span;
use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Base\Link;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Article\ArticleBox as ArticleBoxPath;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Article\ArticleBoxPrice as ArticleBoxPricePath;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Article\ArticleBoxTitleLink as ArticleBoxTitleLinkPath;
use OxidEsales\TestingLibrary\oxComponents\Base\Select;

class ArticleBox extends Component
{
    /**
     * @return ArticleBoxPath
     */
    public function getPath()
    {
        return new ArticleBoxPath($this->getIdentity());
    }

    /**
     * @param $iVariant
     */
    public function selectVariant($iVariant)
    {
        $oSelect = new Select($this->getNode());
        $oSelect->select($iVariant);
    }

    /**
     * @return Link
     */
    public function getTitleLink()
    {
        $oLink = new Link($this->getNode());
        $oLink->constructWithCustomPath(new ArticleBoxTitleLinkPath());

        return $oLink;
    }

    public function getPriceInformation($identifier = null)
    {
        $priceSpan = new Span($this->getNode(), 'lead text-nowrap');
        $priceSpan->constructWithCustomPath(new ArticleBoxPricePath($identifier));

        return $priceSpan->getText();
    }
}