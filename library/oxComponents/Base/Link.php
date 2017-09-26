<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Base;

use OxidEsales\TestingLibrary\oxComponents\Paths\Base\Link as LinkPath;

/**
 * Class input
 * @package OxidEsales\TestingLibrary\oxComponents\Base
 */
class Link extends Base
{
    /**
     * Returns component path.
     *
     * @return LinkPath
     */
    public function getPath()
    {
        return new LinkPath($this->getIdentity());
    }
}