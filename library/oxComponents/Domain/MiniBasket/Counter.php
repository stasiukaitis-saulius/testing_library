<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\MiniBasket;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Base\Span;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\MiniBasket\Counter as CounterPath;

class Counter extends Component
{
    /**
     * Returns component path.
     *
     * @return object|CounterPath
     */
    public function getPath()
    {
        return new CounterPath();
    }

    /**
     * @return null|integer
     */
    public function getCount()
    {
        $oNode = $this->getNode();
        if (is_null($oNode)) {
            $iCount = null;
        } else {
            $oSpan = new Span($oNode, 'countValue');
            $iCount = $oSpan->getText();
        }

        return $iCount;
    }
}