<?php
/**
* #PHPHEADER_OXID_LICENSE_INFORMATION#
*/

namespace OxidEsales\TestingLibrary\oxComponents\Domain;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\ContentBox as ContentBoxPath;

class ContentBox extends Component
{
    public function getPath()
    {
        return new ContentBoxPath();
    }
}