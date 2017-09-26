<?php
/**
* #PHPHEADER_OXID_LICENSE_INFORMATION#
*/

namespace OxidEsales\TestingLibrary\oxComponents\Domain\MiniBasket;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\MiniBasket\FlyOutBox as FlyOutBoxPath;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\MiniBasket\DisplayCartLink as DisplayCartLinkPath;

/**
 * Class FlyOut
 * @package OxidEsales\TestingLibrary\oxComponents\Domain\Main\MiniBasket
 */
class FlyOutBox extends Component
{
    /**
     * Returns component path.
     *
     * @return object|FlyOutBoxPath
     */
    public function getPath()
    {
        return new FlyOutBoxPath();
    }

    /**
     * Clicks "Display cart" link.
     */
    public function clickDisplayCartLink()
    {
        $this->findNode(new DisplayCartLinkPath())->click();
    }
}