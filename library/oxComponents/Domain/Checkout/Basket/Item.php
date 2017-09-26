<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\Checkout\Basket;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Base\Link;
use OxidEsales\TestingLibrary\oxComponents\Base\Select;
use OxidEsales\TestingLibrary\oxComponents\Base\Input;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\Basket\Item as ItemPath;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\Basket\ItemPart as ItemPartPath;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\Basket\ItemQuantityInput as ItemQuantityInputPath;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\Basket\PersistentParameterInput as PersistentParameterInputPath;

/**
 * Class Item
 * @package OxidEsales\TestingLibrary\oxComponents\Domain\Checkout\Steps\Cart\Basket
 */
class Item extends Component
{
    /**
     * @return ItemPath
     */
    public function getPath()
    {
        return new ItemPath($this->getIdentity());
    }

    /**
     * @return Link
     */
    public function getArticleTitleLink()
    {
        return new Link($this->findNode(new ItemPartPath(3)));
    }

    /**
     * @return Select
     */
    public function getSelect()
    {
        return new Select($this->getNode());
    }

    public function getPersistentParameterInput()
    {
        return new Input($this->findNode(new PersistentParameterInputPath()));
    }

    /**
     * @return Input
     */
    public function getQuantityInput()
    {
        return new Input($this->findNode(new ItemQuantityInputPath()));
    }
}