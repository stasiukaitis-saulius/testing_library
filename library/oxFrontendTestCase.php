<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 *
 * @link http://www.oxid-esales.com
 * @copyright (c) OXID eSales AG 2003-#OXID_VERSION_YEAR#
 */

require_once 'oxTestCase.php';

class oxFrontendTestCase extends oxTestCase
{
    /* --------------------- eShop frontend side only functions ---------------------- */

    /**
     * opens shop frontend and runs checkForErrors().
     *
     * @param bool $blForceMainShop opens main shop even if SubShop is being tested
     *
     * @return null
     */
    public function openShop( $blForceMainShop = false )
    {
        $this->openNewWindow( shopURL , false );

        /* if ( OXID_VERSION_EE ) : */
        if ( isSUBSHOP ) {
            if (!$blForceMainShop) {
                $this->clickAndWait("link=subshop");
            } else {
                $sShopNr = $this->getShopVersionNumber();
                $this->clickAndWait("link=OXID eShop ". $sShopNr);
            }
        }
        /* endif; */
        $this->checkForErrors();
    }

    /**
     * Selects shop language in frontend.
     *
     * @param string $sLanguage language title.
     */
    public function switchLanguage($sLanguage)
    {
        $this->click("languageTrigger");
        $this->waitForItemAppear("languages");
        $this->clickAndWait("//ul[@id='languages']//li/a/span[text()='".$sLanguage."']");
    }

    /**
     * Selects shop currency in frontend.
     *
     * @param string $sCurrency currency title.
     */
    public function switchCurrency($sCurrency)
    {
        $this->click("//p[@id='currencyTrigger']/a");
        $this->waitForItemAppear("currencies");
        $this->clickAndWait("//ul[@id='currencies']//*[text()='$sCurrency']");
    }

    /**
     * Login customer by using login fly out form.
     *
     * @param string $userName user name (email).
     * @param string $userPass user password.
     * @param boolean $waitForLogin if needed to wait until user get logged in.
     * @return null
     */
    public function loginInFrontend($userName, $userPass, $waitForLogin = true)
    {
        $this->selectWindow(null);
        $this->click("//ul[@id='topMenu']/li[1]/a");
        try {
            $this->waitForItemAppear("loginBox", 2);
        } catch( Exception $e ) {
            $this->click("//ul[@id='topMenu']/li[1]/a");
            $this->waitForItemAppear("loginBox", 2);
        }
        $this->type("//div[@id='loginBox']//input[@name='lgn_usr']", $userName);
        $this->type("//div[@id='loginBox']//input[@name='lgn_pwd']", $userPass);

        $this->clickAndWait("//div[@id='loginBox']//button[@type='submit']");
        if ($waitForLogin) {
            $this->waitForElement("//a[@id='logoutLink']");
        }
    }

    /**
     * Open article page.
     *
     * @param string|int $sArticleId Article id
     * @param bool $blClearCache
     * @param string $sShopId
     */
    public function openArticle( $sArticleId, $blClearCache = false, $sShopId = null )
    {
        $aParams = array(
            'cl' => 'details',
            'anid' => $sArticleId,
        );

        $this->openNewWindow( $this->_getShopUrl( $aParams, $sShopId ), $blClearCache );
    }

    /**
     * Adds article to basket
     *
     * @param string|int $sArticleId   Article id
     * @param int $iAmount             Amount of items to add
     * @param string $sController      Controller name which should be opened after article is added
     * @param array $aAdditionalParams Additional parameters (like persparam[details] for label)
     * @param int $sShopId             Shop id
     */
    public function addToBasket( $sArticleId, $iAmount = 1, $sController = 'basket', $aAdditionalParams = array(), $sShopId = null )
    {
        $aParams['cl'] = $sController;
        $aParams['fnc'] = 'tobasket';
        $aParams['aid'] = $sArticleId;
        $aParams['am'] = $iAmount;
        $aParams['anid'] = $sArticleId;

        $aParams = array_merge( $aParams, $aAdditionalParams );

        $this->openNewWindow( $this->_getShopUrl( $aParams, $sShopId ), false );
    }

    /**
     * Opens subshop frontend and switch to EN language.
     *
     * @return null
     */
    public function openSubshopFrontend()
    {
        $this->openShop( '', false );
        $this->clickAndWait( "link=subshop" );
    }

    /**
     * mouseOver element and then click specified link.
     *
     * @param string $element1 mouseOver element.
     * @param string $element2 clickable element.
     * @return null
     */
    public function mouseOverAndClick($element1, $element2)
    {
        $this->mouseOver($element1);
        $this->waitForItemAppear($element2);
        $this->clickAndWait($element2);
    }

    /**
     * Performs search for selected parameter.
     *
     * @param string $searchParam search parameter.
     * @return null
     */
    public function searchFor($searchParam)
    {
        $this->type("//input[@id='searchParam']", $searchParam);
        $this->keyPress("searchParam", "\\13"); //presing enter key
        $this->waitForPageToLoad( 10000 );
        $this->checkForErrors();
    }

    /**
     * Opens basket.
     *
     * @param string $language  active language in shop.
     * @return null
     */
    public function openBasket($language="English")
    {
        if ($language == 'Deutsch') {
            $sLink = "Warenkorb zeigen";
        } else {
            $sLink = "Display Cart";
        }
        $this->click("//div[@id='miniBasket']/img");
        $this->waitForItemAppear("//div[@id='basketFlyout']//a[text()='".$sLink."']");
        $this->clickAndWait("//div[@id='basketFlyout']//a[text()='".$sLink."']");
    }

    /**
     * Selects specified value from dropdown (sorting, items per page etc).
     *
     * @param int    $elementId  drop down element id.
     * @param string $itemValue  item to select.
     * @param string $extraIdent additional identification for element.
     * @return null
     */
    public function selectDropDown($elementId, $itemValue='', $extraIdent='')
    {
        $this->assertElementPresent($elementId);
        $this->assertFalse($this->isVisible("//div[@id='".$elementId."']//ul"));
        $this->click("//div[@id='".$elementId."']//p");
        $this->waitForItemAppear("//div[@id='".$elementId."']//ul");
        if ('' == $itemValue) {
            $this->clickAndWait("//div[@id='".$elementId."']//ul/".$extraIdent."/a");
        } else {
            $this->clickAndWait("//div[@id='".$elementId."']//ul/".$extraIdent."/a[text()='".$itemValue."']");
        }
    }

    /**
     * Selects specified value from dropdown (for multidimensional variants).
     *
     * @param string $elementId  container id.
     * @param int    $elementNr  select list number (e.g. 1, 2).
     * @param string $itemValue  item to select.
     * @param string $textMsg    text that must appear after selecting md variant.
     * @return null
     */
    public function selectVariant($elementId, $elementNr, $itemValue, $textMsg='')
    {
        $this->assertElementPresent($elementId);
        $this->assertFalse($this->isVisible("//div[@id='".$elementId."']/div[".$elementNr."]//ul"));
        $this->click("//div[@id='".$elementId."']/div[".$elementNr."]//p");
        $this->waitForItemAppear("//div[@id='".$elementId."']/div[".$elementNr."]//ul");
        $this->click("//div[@id='".$elementId."']/div[".$elementNr."]//ul//a[text()='".$itemValue."']");
        if (!empty($textMsg)) {
            $this->waitForText($textMsg);
        } else {
            $this->waitForPageToLoad(10000);
        }
    }

}
