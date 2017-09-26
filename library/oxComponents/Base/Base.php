<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */
namespace OxidEsales\TestingLibrary\oxComponents\Base;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class Base extends Component
{
    /**
     * Returns component path.
     *
     * @return Path
     */
    public function getPath()
    {
        return new Path();
    }

    /**
     * Clicks element.
     */
    public function click()
    {
        $this->getNode()->click();
    }

    /**
     * Function returns element text.
     *
     * @return null|string
     */
    public function getText()
    {
        return $this->getNode()->getText();
    }
}