<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Base;

use OxidEsales\TestingLibrary\oxComponents\Paths\Base\Select\Select as SelectPath;
use OxidEsales\TestingLibrary\oxComponents\Paths\Base\Select\SelectionButton as SelectionButtonPath;
use OxidEsales\TestingLibrary\oxComponents\Paths\Base\Select\SelectionElement as SelectionElementPath;
use OxidEsales\TestingLibrary\oxComponents\Paths\Base\Select\SelectionElementByName as SelectionElementByNamePath;
use OxidEsales\TestingLibrary\oxComponents\Paths\Base\Select\SelectionSelectedElement as SelectionSelectedElementPath;

/**
 * Class Select
 * @package OxidEsales\TestingLibrary\oxComponents\Base
 */
class Select extends Base
{
    /**
     * Returns component path.
     *
     * @return SelectPath
     */
    public function getPath()
    {
        return new SelectPath();
    }

    /**
     * Selects selects variant by given number.
     *
     * @param $iVariantNumber
     */
    public function select($iVariantNumber)
    {
        $iVariantNumber++;
        $oNodeButton = $this->findNode(new SelectionButtonPath());
        $oNodeButton->click();
        $oNodeListElement = $this->findNode(new SelectionElementPath($iVariantNumber));
        $oNodeListElement->click();
    }

    /**
     * Selects selects variant by given number.
     *
     * @param $elementName
     */
    public function selectByName($elementName)
    {
       /* $oNodeButton = $this->findNode(new SelectionButtonPath());
        $oNodeButton->click();*/
        $oNodeListElement = $this->findNode(new SelectionElementByNamePath($elementName));
        $oNodeListElement->click();
    }

    /**
     * Function returns selected element text.
     *
     * @return null|string
     */
    public function getSelectedText()
    {
        $sSelectionText = $this->findNode(new SelectionSelectedElementPath())->getText();

        return $sSelectionText;
    }
}