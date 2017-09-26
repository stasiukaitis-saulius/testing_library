<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\MiniBasket;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Paths\Base\Img as ImgPath;

class Icon extends Component
{
    /**
     * Returns component path.
     *
     * @return object|ImgPath
     */
    public function getPath()
    {
        return new ImgPath('minibasketIcon');
    }

    /**
     * Clicks mini basket icon.
     */
    public function click()
    {
        $this->getNode()->click();
    }
}