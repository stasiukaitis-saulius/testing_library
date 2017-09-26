<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\Checkout\Basket;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Base\Button;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\Basket\Functions as FunctionsPath;

class Functions extends Component
{
    /**
     * Returns component path.
     *
     * @return object|FunctionsPath
     */
    public function getPath()
    {
        return new FunctionsPath();
    }

    /**
     * @return Button
     */
    public function getUpdateButton()
    {
        return new Button($this->getNode(), 'basketUpdate');
    }
}