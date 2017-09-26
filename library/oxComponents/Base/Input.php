<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Base;

use OxidEsales\TestingLibrary\oxComponents\Paths\Base\Input as InputPath;

/**
 * Class input
 * @package OxidEsales\TestingLibrary\oxComponents\Base
 */
class Input extends Base
{
    /**
     * Returns component path.
     *
     * @return InputPath
     */
    public function getPath()
    {
        return new InputPath($this->getIdentity());
    }

    /**
     * @param $sText
     */
    public function setValue($sText)
    {
        $this->getNode()->setValue($sText);
    }

    /**
     * @return null|string
     */
    public function getValue()
    {
        return $this->getNode()->getValue();
    }

    /**
     * @param $iKeyId
     */
    public function keyPress($iKeyId)
    {
        $this->getNode()->keyPress($iKeyId);
    }
}