<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Base;

use OxidEsales\TestingLibrary\oxComponents\Paths\Base\Img as ImgPath;

/**
 * Class Img
 * @package OxidEsales\TestingLibrary\oxComponents\Base
 */
class Img extends Base
{
    /**
     * Returns component path.
     *
     * @return ImgPath
     */
    public function getPath()
    {
        return new ImgPath();
    }
}