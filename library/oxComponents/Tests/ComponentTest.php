<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

class ArticleBoxTest extends PHPUnit_Framework_TestCase
{
    public function testFindFunctionToCheckException()
    {
        $oComponent = $this->getMock('\OxidEsales\TestingLibrary\oxComponents\Component', array('getNode', 'getPath'), array(), '', false);
        $oComponent->expects($this->any())->method('getNode')->will($this->returnValue($this->_getNode()));

        $oPath = new \OxidEsales\TestingLibrary\oxComponents\Paths\Main\Search();
        $sClassName = get_class($oComponent);
        $this->setExpectedException('Exception', "Node not found by path: " . (string)$oPath . "; Test failed in component: $sClassName");
        $oComponent->findNode($oPath);
    }

    public function testFindFunctionToCheckReturnedElement()
    {
        $sResult = 'NodeElementObject';
        $oComponent = $this->getMock('\OxidEsales\TestingLibrary\oxComponents\Component', array('getNode', 'getPath'), array(), '', false);
        $oComponent->expects($this->any())->method('getNode')->will($this->returnValue($this->_getNode($sResult)));

        $oPath = new \OxidEsales\TestingLibrary\oxComponents\Paths\Main\Search();
        $this->assertSame($sResult, $oComponent->findNode($oPath));
    }

    /**
     * Returns mocked node element.
     *
     * @param null $sReturnedFindFunctionValue
     * @return object
     */
    private function _getNode($sReturnedFindFunctionValue = null)
    {
        $oNode = $this->getMock('\Behat\Mink\Element\NodeElement', array('find'), array(), '', false);
        $oNode->expects($this->any())->method('find')->will($this->returnValue($sReturnedFindFunctionValue));

        return $oNode;
    }
}
 