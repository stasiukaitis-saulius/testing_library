<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Base\Button;
use OxidEsales\TestingLibrary\oxComponents\Base\Link;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Header as HeaderPath;

class Header extends Component
{
    /**
     * @return object|HeaderPath
     */
    public function getPath()
    {
        return new HeaderPath();
    }

    /**
     * @return Link
     */
    public function getLogInLink()
    {
        return new Link($this->getNode(), 'loginBoxOpener');
    }

}