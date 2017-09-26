<?php

/**
* #PHPHEADER_OXID_LICENSE_INFORMATION#
*/

namespace OxidEsales\TestingLibrary\oxComponents;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;
use Behat\Mink\Element\Element;

/**
 * Class Component
 * @package library\components
 */
abstract class Component
{
    /**
     * Node element.
     *
     * @var \Behat\Mink\Element\NodeElement|null
     */
    protected $_oElement;

    /**
     * Parent node element.
     *
     * @var \Behat\Mink\Element\NodeElement|null
     */
    protected $_oParentElement;

    /**
     * Used to specify path by this identity.
     *
     * @var mixed|null
     */
    protected $_mxIdentity;

    /**
     * @var Path
     */
    protected $_oPath = null;

    /**
     * @param \Behat\Mink\Element\Element $oElement
     * @param null $sIdentity
     */
    public function __construct(Element $oElement, $sIdentity = null)
    {
        $this->_mxIdentity = $sIdentity;
        $this->setNode($oElement);
        $this->setParentNode($oElement);
        if ((string) $this->getPath() != '') {
            $oNode = $oElement->find('xpath', $this->getPath());
            $this->setNode($oNode);
        }
    }

    /**
     * Sets component path.
     */
    public function setPath($oPath)
    {
        $this->_oPath = $oPath;
    }

    /**
     * Returns path object.
     *
     * @return Path
     */
    abstract public function getPath();

    /**
     * Returns node.
     *
     * @return \Behat\Mink\Element\NodeElement|null
     */
    public function getNode()
    {
        return $this->_oElement;
    }

    /**
     * Sets node element.
     *
     * @param \Behat\Mink\Element\Element $oNode
     */
    public function setNode($oNode)
    {
        $this->_oElement = $oNode;
    }

    /**
     * @param \Behat\Mink\Element\Element $oParentElement
     */
    public function setParentNode($oParentElement)
    {
        $this->_oParentElement = $oParentElement;
    }

    /**
     * @return \Behat\Mink\Element\NodeElement|null
     */
    public function getParentNode()
    {
        return $this->_oParentElement;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->_mxIdentity;
    }

    /**
     * Finds node by given path.
     *
     * @param Path $oPath
     * @return \Behat\Mink\Element\NodeElement|null
     * @throws \Exception
     */
    public function findNode(Path $oPath)
    {
        $oNode = $this->getNode()->find('xpath',  $oPath);
        if(is_null($oNode)){
            throw new \Exception("Node not found by path: $oPath; Test failed in component: " . get_called_class());
        }

        return $oNode;
    }

    /**
     * @param Path $oPath
     */
    public function constructWithCustomPath(Path $oPath)
    {
        $oNode = $this->getParentNode()->find('xpath', $oPath);
        $this->setNode($oNode);
    }
}