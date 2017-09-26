<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\Article\Details\Information;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Base\Select;
use OxidEsales\TestingLibrary\oxComponents\Base\Button;
use OxidEsales\TestingLibrary\oxComponents\Base\Input;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Article\Details\Information\Main as MainPath;

/**
 * Class Main
 * @package OxidEsales\TestingLibrary\oxComponents\Domain\Article\Details\Information
 */
class Main extends Component
{
    /**
     * Returns component path.
     *
     * @return mixed|MainPath
     */
    public function getPath()
    {
        return new MainPath();
    }

    /**
     * Returns selection component.
     *
     * @param $iSelectionNumber
     *
     * @return select
     */
    public function getSelections($iSelectionNumber)
    {
        return new Select($this->getNode(), $iSelectionNumber);
    }

    /**
     * Returns basket button component.
     *
     * @return button
     */
    public function getAddToBasketButton()
    {
        return new Button($this->getNode(), 'toBasket');
    }

    /**
     * Returns persistent parameter component.
     *
     * @return Input
     */
    public function getPersistentParameterInput()
    {
        return new Input($this->getNode(), 'persparam[details]');
    }
}