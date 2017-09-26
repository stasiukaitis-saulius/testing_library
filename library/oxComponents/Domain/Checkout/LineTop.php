<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\Checkout;

use OxidEsales\TestingLibrary\oxComponents\Base\Button;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\LineTop as LineTopPath;

class LineTop extends Checkout
{
    /**
     * @return Button
     */
    public function getNextStepButton()
    {
        return new Button($this->_getLineTopNode());
    }

    /**
     * @return \Behat\Mink\Element\NodeElement|null
     */
    private function _getLineTopNode()
    {
        return $this->findNode(new LineTopPath());
    }
}