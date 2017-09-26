<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\MiniBasket;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\MiniBasket\MiniBasket as MiniBasketPath;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\MiniBasket\Counter as CounterPath;

class MiniBasket extends Component
{
    /**
     * Returns component path.
     *
     * @return object|MiniBasketPath
     */
    public function getPath()
    {
        return new MiniBasketPath();
    }

    /**
     * @return Counter
     */
    public function getCounter()
    {
        return new Counter($this->getNode());
    }

    /**
     * @return Icon
     */
    public function getIcon()
    {
        return new Icon($this->getNode());
    }

    /**
     * @return FlyOutBox
     */
    public function getFlyOutBox()
    {
        return new FlyOutBox($this->getNode());
    }
}