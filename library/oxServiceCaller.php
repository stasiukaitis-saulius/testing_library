<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 *
 * @link http://www.oxid-esales.com
 * @copyright (c) OXID eSales AG 2003-#OXID_VERSION_YEAR#
 */

class oxServiceCaller {

    /**
     * @var array
     */
    private $_aParameters = array();

    /**
     * Sets given parameters.
     *
     * @param $sKey
     * @param $aVal
     */
    public function setParameter( $sKey, $aVal )
    {
        $this->_aParameters[$sKey] = $aVal;
    }

    /**
     * Returns array of parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->_aParameters;
    }

    /**
     * Call shop service to execute code in shop.
     * @example call to update information to database.
     *
     * @param string $sServiceName
     * @param string $sShopId
     *
     * @throws oxException
     *
     * @return string $sResult
     */
    public function callService($sServiceName, $sShopId = null)
    {
        if ($sShopId && oxSHOPID != 'oxbaseshop') {
            $this->setParameter('shp', $sShopId);
        } elseif (isSUBSHOP) {
            $this->setParameter('shp', oxSHOPID);
        }

        $oCurl = new oxCurl();

        $sShopUrl = shopURL.'/Services/service.php';
        $this->setParameter('service', $sServiceName);

        $oCurl->setUrl($sShopUrl);
        $oCurl->setParameters($this->getParameters());

        return unserialize($oCurl->execute());
    }
}