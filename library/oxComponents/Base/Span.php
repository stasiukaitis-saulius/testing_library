<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Base;

use OxidEsales\TestingLibrary\oxComponents\Paths\Base\Span as SpanPath;

/**
 * Class input
 * @package OxidEsales\TestingLibrary\oxComponents\Base
 */
class Span extends Base
{
    /**
     * Returns component path.
     *
     * @return SpanPath
     */
    public function getPath()
    {
        return new SpanPath($this->getIdentity());
    }
}