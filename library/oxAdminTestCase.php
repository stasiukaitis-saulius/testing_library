<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 *
 * @link http://www.oxid-esales.com
 * @copyright (c) OXID eSales AG 2003-#OXID_VERSION_YEAR#
 */

require_once 'oxTestCase.php';

class oxAdminTestCase extends oxTestCase
{

    /* -------------------------- Admin side only functions ------------------------ */

    /**
     * login to admin with default admin pass and opens needed menu.
     *
     * @param string $menuLink1     menu link (e.g. master settings, shop settings).
     * @param string $menuLink2     sub menu link (e.g. administer products, discounts, vat).
     * @param bool   $forceMainShop force main shop.
     * @param string $user          shop admin username.
     * @param string $pass          shop admin password.
     * @param string $language      shop admin language.
     * @return null
     */
    public function loginAdmin($menuLink1 = null, $menuLink2 = null, $forceMainShop=false, $user="admin@myoxideshop.com", $pass="admin0303", $language = "English")
    {
        $this->openNewWindow(shopURL."admin");
        $this->waitForElement('usr');
        $this->waitForElement('pwd');
        $this->type("usr", $user);
        $this->type("pwd", $pass);
        $this->select("lng", "$language");
        $this->select("prf", "Standard");
        $this->clickAndWait("//input[@type='submit']");

        $this->frame("navigation");

        /* if ( OXID_VERSION_EE ) : */
        if (isSUBSHOP && !$forceMainShop) { // selecting active subshop
            $this->selectAndWaitFrame("selectshop", "label=subshop", "basefrm");
        }
        /* endif; */

        if ($menuLink1 && $menuLink2) {
            $this->selectMenu( $menuLink1, $menuLink2 );
        } else {
            $this->frame("basefrm");
        }
    }

    /**
     * login to admin for PayPal shop with admin pass and opens needed menu.
     *
     * @param string $menuLink1     menu link (e.g. master settings, shop settings).
     * @param string $menuLink2     sub menu link (e.g. administer products, discounts, vat).
     * @param string $editElement   element to check in edit frame (optional).
     * @param string $listElement   element to check in list frame (optional).
     * @param bool   $forceMainShop force main shop.
     * @param string $user          shop admin username.
     * @param string $pass          shop admin password.
     * @param string $language          shop admin language.
     * @return null
     */
    public function loginAdminForModule($menuLink1, $menuLink2, $editElement=null, $listElement=null, $forceMainShop=false, $user="admin", $pass="admin", $language = "English")
    {
        $this->loginAdmin($menuLink1, $menuLink2, $forceMainShop, $user, $pass, $language);
    }

    /**
     * login to admin with admin pass, selects subshop and opens needed menu.
     * @param string $menuLink1 menu link (e.g. master settings, shop settings).
     * @param string $menuLink2 sub menu link (e.g. administer products, discounts, vat).
     * @param string $user          shop admin username.
     * @param string $pass          shop admin password.
     * @return null
     */
    public function loginSubshopAdmin($menuLink1, $menuLink2, $user="admin@myoxideshop.com", $pass="admin0303")
    {
        $this->openNewWindow(shopURL."admin");
        $this->waitForElement('user');
        $this->waitForElement('pwd');
        $this->type("user", $user);
        $this->type("pwd", $pass);
        $this->select("chlanguage", "label=English");
        $this->select("profile", "label=Standard");
        $this->clickAndWait("//input[@type='submit']");

        $this->frame("navigation");

        $this->selectAndWaitFrame("selectshop", "label=subshop", "basefrm");

        $this->selectMenu( $menuLink1, $menuLink2 );
    }

    /**
     * login to trusted shops in admin.
     * @param string $link1
     * @param string $link2
     * @param string $user          shop admin username.
     * @param string $pass          shop admin password.
     * @return null
     */
    public function loginAdminTs($link1 = "link=Seal of quality", $link2 = "link=Trusted Shops", $user="admin@myoxideshop.com", $pass="admin0303")
    {
        oxDb::getInstance()->getDb()->Execute("UPDATE `oxconfig` SET `OXVARVALUE` = 0xce92 WHERE `OXVARNAME` = 'sShopCountry';");

        $this->openNewWindow(shopURL."admin");
        $this->type("user", $user);
        $this->type("pwd", $pass);
        $this->select("chlanguage", "label=English");
        $this->select("profile", "label=Standard");
        $this->clickAndWait("//input[@type='submit']");

        $this->frame("navigation");

        /* if ( OXID_VERSION_EE ) : */
        if ( isSUBSHOP ) { // selecting active subshop
            $this->selectAndWaitFrame("selectshop", "label=subshop", "edit");
        }
        /* endif; */
        $this->waitForElement($link1);
        $this->click($link1);
        $this->click($link2);

        $this->waitForFrameToLoad('basefrm', 10000);

        //testing edit frame for errors
        $this->frame("edit");
    }

    /**
     * selects other menu in admin interface.
     *
     * @param string $menuLink1   menu link (e.g. master settings, shop settings).
     * @param string $menuLink2   sub menu link (e.g. administer products, discounts, vat).
     */
    public function selectMenu($menuLink1, $menuLink2)
    {
        $this->selectWindow(null);

        $this->frame('navigation');

        $this->waitForElement("link=".$menuLink1);
        $this->click("link=".$menuLink1);
        $this->click("link=".$menuLink2);

        $this->frame("relative=top");
        $this->waitForFrameToLoad('basefrm', 5000);
        $this->frame( "basefrm" );
        if ( $this->isElementPresent('edit') ) {
            $this->waitForFrameToLoad('edit', 5000, true);
            $this->frame("edit");
            $sFrameToLoad = "list";
        } else {
            $sFrameToLoad = $this->isElementPresent('list')? 'list' : 'basefrm';
        }

        $this->frame( $sFrameToLoad );
    }

    /**
     * Logs out of admin
     *
     * @param string $sLocator logout link locator
     */
    public function logoutAdmin( $sLocator = "link=Logout" )
    {
        $this->frame("header");
        $this->waitForElement( $sLocator );
        $this->click( $sLocator );

        try {
            $this->waitForPageToLoad( 10000 );
        } catch ( Exception $e ) {
            $this->openNewWindow(shopURL."admin");
        }

        $this->checkForErrors();
    }

    /**
     * downloads eFire connector.
     *
     * @param string $sNameEfi user name for eFire.
     * @param string $sPswEfi  user password for eFire.
     * @param string $user     user name for login to shop admin.
     * @param string $pass     user password for login to shop admin.
     * @return null
     */
    public function downloadConnector($sNameEfi, $sPswEfi, $user="admin@myoxideshop.com", $pass="admin0303")
    {
        $this->frame("navigation");
        $this->waitForElement("link=OXID eFire");
        $this->checkForErrors();
        $this->click("link=OXID eFire");
        $this->clickAndWaitFrame("link=Shop connector", "edit");

        //testing edit frame for errors
        $this->frame("edit");
        $this->assertTextNotPresent("Shop connector downloaded successfully");
        $this->type("etUsername", $sNameEfi);
        $this->type("etPassword", $sPswEfi);
        $this->clickAndWait("etSubmit");
        $this->assertTextPresent("Shop connector downloaded successfully", "connector was not downloaded successfully");
        $this->clearCache();
        echo " connector downloaded successfully. ";
    }

    /**
     * select frame in Admin interface.
     *
     * @param string $sFrame name of needed admin frame.
     * @param bool $blForceReselect Switches frame even if it is currently selected
     */
    public function frame( $sFrame, $blForceReselect = false )
    {
        if ( !$blForceReselect && $this->getSelectedFrame() == $sFrame ) {
            return;
        }

        $this->selectFrame("relative=top");

        if ( $sFrame == "relative=top" ) {
            return;
        }

        $aFramePaths = array(
            "edit" => "basefrm/edit",
            "list" => "basefrm/list",
            "navigation" => "navigation/adminnav",
        );

        if ( $aFramePaths[$sFrame] ) {
            $aPath = explode( "/", $aFramePaths[$sFrame] );
            foreach ( $aPath as $sCurrentFrame ) {
                $this->waitForElement( $sCurrentFrame, 5 );
                $this->selectFrame( $sCurrentFrame );
            }
        } else {
            $this->waitForElement( $sFrame, 5 );
            $this->selectFrame( $sFrame );
        }

        $this->checkForErrors();
    }

    /**
     * Clicks new item button
     *
     * @param string $sButtonSelector
     */
    public function clickCreateNewItem( $sButtonSelector = "btn.new" )
    {
        $this->frame( 'relative=top' );
        $this->click( $sButtonSelector );
        $this->waitForFrameToLoad( 'list', 5000 );
        $this->waitForFrameToLoad( 'edit', 5000, true );
        $this->frame( 'edit' );
    }

    /**
     * Opens admin list item. Activates edit frame after
     *
     * @param string $sSorterSelector
     */
    public function changeListSorting( $sSorterSelector )
    {
        $this->frame('list');
        $this->clickAndWaitFrame( $sSorterSelector );
        $this->checkForErrors();
    }

    /**
     * Opens admin list item. Activates edit frame after
     *
     * @param string $sItemName
     * @param string $sSearchColumn
     */
    public function openListItem( $sItemName, $sSearchColumn = '' )
    {
        $this->frame('list');
        $sItemLocator = ((strpos($sItemName, 'link=') === false )? 'link=' : '') . $sItemName;

        if ( $sSearchColumn && !$this->isElementPresent( $sItemLocator ) ) {
            $this->type("where$sSearchColumn", $sItemName);
            $this->clickAndWaitFrame( 'submitit' );
        }
        $this->clickAndWaitFrame( $sItemLocator, 'edit' );
        $this->frame('edit');
        $this->checkForErrors();
    }

    /**
     * Opens admin list item. Activates edit frame after
     *
     * @param string $sPageSelector
     */
    public function openListPage( $sPageSelector )
    {
        $this->frame('list');
        $this->clickAndWaitFrame( $sPageSelector );
        $this->checkForErrors();
    }

    /**
     * clicks entered link in list frame and selects edit frame.
     *
     * @param string $sTabName tab name that should be opened.
     */
    public function openTab( $sTabName )
    {
        $this->frame('list');
        $sTabName = "//div[@class='tabs']//a[text()='$sTabName']";
        $this->clickAndWaitFrame( $sTabName, 'edit' );
        $this->frame('edit');
    }



    /**
     * Opens admin list item. Activates edit frame after
     *
     * @param string $sLanguage
     * @param string $sSelectLocator
     */
    public function changeAdminListLanguage( $sLanguage, $sSelectLocator = 'changelang' )
    {
        $sSelectedFrame = $this->getSelectedFrame();
        $this->frame('list');
        $this->_changeAdminLanguage( $sLanguage, $sSelectLocator );
        $this->frame($sSelectedFrame);
    }

    /**
     * Opens admin list item. Activates edit frame after
     *
     * @param string $sLanguage
     * @param string $sSelectLocator
     */
    public function changeAdminEditLanguage( $sLanguage, $sSelectLocator = 'subjlang' )
    {
        $this->frame('edit');
        $this->_changeAdminLanguage( $sLanguage, $sSelectLocator );
    }

    /**
     * Selects language and checks if it stays selected. If not - re-selects.
     *
     * @param string $sSelectLocator
     * @param string $sLanguage
     */
    protected function _changeAdminLanguage( $sLanguage, $sSelectLocator )
    {
        $this->selectAndWaitFrame($sSelectLocator, "label=$sLanguage", "edit");
        if ( $this->getSelectedLabel( $sSelectLocator ) != $sLanguage ) {
            $this->selectAndWaitFrame($sSelectLocator, "label=$sLanguage", "edit");
        }
        $this->checkForErrors();
    }

    /**
     * Clicks delete item button in list
     */
    public function clickDeleteListItem( $sId = 1 )
    {
        $this->frame('list');
        $this->clickAndConfirm("del.$sId", "edit");
    }

    /**
     * Selects popUp window and waits till it is fully loaded.
     *
     * @param string $popUpElement element used to check if popUp is fully loaded.
     * @return null
     */
    public function usePopUp($popUpElement="//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]")
    {
        $this->waitForPopUp("ajaxpopup", 15000);
        $this->selectWindow("ajaxpopup");
        $this->windowMaximize("ajaxpopup");
        $this->waitForElement($popUpElement);
        $this->checkForErrors();
    }

    /**
     * Waits for element to show up in specific place.
     *
     * @param string $value   expected text to show up.
     * @param string $locator place where specified text must show up.
     * @param int $iTimeToWait timeout
     * @return null
     */
    public function waitForAjax( $value, $locator, $iTimeToWait = 20 )
    {
        $iTimeToWait = $iTimeToWait * $this->_iWaitTimeMultiplier;
        for ($iSecond = 0; $iSecond <= $iTimeToWait; $iSecond++) {
            try {
                if ( $this->isElementPresent($locator) && $value == $this->getText($locator) ) {
                    return ;
                }
            } catch (Exception $e) {}
            if ($iSecond >= $iTimeToWait ) {
                $this->fail("Ajax timeout while waiting for '${locator}' or value is not equal to '${value}' ");
            }
            usleep(500000);
        }
    }

    /**
     * Drags and drops element to specified location.
     *
     * @param string $item      element which will be dragged and dropped.
     * @param string $container place where to drop specified element.
     * @return null
     */
    public function dragAndDrop($item, $container)
    {
        $this->click($item);
        $this->checkForErrors();
        $this->dragAndDropToObject($item, $container);
    }

}
