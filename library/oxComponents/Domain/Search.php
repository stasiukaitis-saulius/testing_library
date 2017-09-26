<?php
/**
* #PHPHEADER_OXID_LICENSE_INFORMATION#
*/

namespace OxidEsales\TestingLibrary\oxComponents\Domain;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Base\Input;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Search as SearchPath;

class Search extends Component
{
    public function getPath()
    {
        return new SearchPath();
    }

    public function searchArticle($sSearch)
    {
        $oInput = new Input($this->getNode(), 'searchparam');
        $oInput->setValue($sSearch);
        $oInput->keyPress(13);
    }
}