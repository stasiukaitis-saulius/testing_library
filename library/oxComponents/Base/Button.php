<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Base;

use OxidEsales\TestingLibrary\oxComponents\Paths\Base\Button as ButtonPath;

/**
 * Class Button
 * @package OxidEsales\TestingLibrary\oxComponents\Base
 */
class Button extends Base
{
    /**
     * Returns component path.
     *
     * @return ButtonPath
     */
    public function getPath()
    {
        return new ButtonPath($this->getIdentity());
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getNode()->getValue();
    }
}