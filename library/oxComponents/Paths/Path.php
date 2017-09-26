<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths;

/**
 * Class Path
 * @package components\paths
 */
class Path
{
    /**
     * @var string|null
     */
    protected $_sXPath = null;

    /**
     * Custom xPath for multiple components //pathPart[%s]otherPart
     *
     * @var string|null
     */
    protected $_sCustomXPath = null;

    /**
     * Identifier for multiple components
     *
     * @var string|null
     */
    protected $_sIdentifier = null;

    /**
     * Identifier setter
     *
     * @param string $sIdentifier - identifier
     */
    public function setIdentifier($sIdentifier)
    {
        $this->_sIdentifier = $sIdentifier;
    }

    /**
     * Identifier getter
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->_sIdentifier;
    }

    /**
     * Constructor
     *
     * @param int $sIdentifier - identifier for multiple components
     */
    public function __construct($sIdentifier = null)
    {
        $this->setIdentifier($sIdentifier);
    }

    /**
     * Form xPath string from object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getXPath();
    }

    /**
     * Returns xPath string
     *
     * @return null|string
     */
    public function getXPath()
    {
        $sXpath = null;
        if (is_null($this->getIdentifier())) {
            $sXpath = $this->_sXPath;
        } elseif(!is_null($this->_sCustomXPath)) {
            $sXpath = sprintf($this->_sCustomXPath, $this->getIdentifier());
        }

        return $sXpath;
    }
}