<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

require_once 'oxMinkWrapper.php';
require_once 'oxServiceCaller.php';
require_once 'oxObjectValidator.php';
require_once 'oxCurl.php';
require_once '../tests/acceptance/oxTranslator.php';

class oxTestCase extends oxMinkWrapper
{

    /**
     * Whether to skip restoring of database on test tear down
     *
     * @var bool
     */
    protected $_blSkipDbRestore  = false;

    /**
     * Whether to skip restoring of database on test tear down
     *
     * @var bool
     */
    protected $_sSelectedFrame  = 'relative=top';

    /**
     * Whether to skip restoring of database on test tear down
     *
     * @var bool
     */
    protected $_sSelectedWindow  = null;

    /**
     * Is logging of function calls length enabled
     */
    protected $_blEnableLog = true;

    /**
     * How much more time wait for these tests.
     */
    protected $_iWaitTimeMultiplier = 1;

    /**
     * @var $_oTranslator object variable
     */
    protected static $_oTranslator = null;

    /**
     * Object validator object
     *
     * @var oxObjectValidator
     */
    protected $_oValidator = null;

    /**
     * @var bool
     */
    protected $_blStartMinkSession = true;

    /**
     * @var string
     */
    protected $_sDriver = 'selenium';

    /**
     * @var \Selenium\Client
     */
    protected $_oClient = null;

    /**
     * Is logging of function calls length enabled
     * @var \Behat\Mink\Session
     */
    protected $_oMinkSession = null;

    /**
     * List of frames.
     * @var array
     */
    protected $_aFramePaths = array(
        "basefrm"        => "basefrm",
        "header"         => "header",
        "edit"           => "basefrm/edit",
        "list"           => "basefrm/list",
        "navigation"     => "navigation/adminnav",
        "adminnav"       => "navigation/adminnav",
        "dynexport_main" => "basefrm/dynexport_main",
        "dynexport_do"   => "basefrm/dynexport_do",
    );

    /**
     * Sets up default environment for tests.
     *
     * @return null
     */
    protected function setUp()
    {
        $this->setTranslator( new oxTranslator() );

        $this->_sSelectedFrame = 'relative=top';
        $this->_sSelectedWindow = null;

        $this->clearTemp();
    }

    /**
     * Restores database after every test.
     *
     * @return null
     */
    protected function tearDown()
    {
        if ( ! ( SKIPSHOPRESTORE || $this->_blSkipDbRestore ) ) {
            $this->restoreDB();
        }

        parent::tearDown();
    }

    /**
     * Runs the bare test sequence.
     */
    public function runBare()
    {
        if ( $this->_blStartMinkSession ) {
            $this->startMinkSession();
        }

        try {
            parent::runBare();
        } catch ( Exception $oException ) {
            $this->getMinkSession()->stop();
            throw $oException;
        }

        $this->getMinkSession()->stop();
    }

    /**
     * Prints error message, closes active browsers windows and stops.
     *
     * @param string    $sErrorMsg       message to display about error place (more easy to find for programmers).
     * @param Exception $oErrorException Exception to throw on error.
     * @return null
     */
    public function stopTesting($sErrorMsg, $oErrorException = null)
    {
        if ($oErrorException) {
            try {
                $this->onNotSuccessfulTest( $oErrorException );
            } catch (Exception $oE) {
                if ($oE instanceof PHPUnit_Framework_ExpectationFailedException) {
                    $sErrorMsg .= "\n\n---\n".$oE->getCustomMessage();
                }
            }
        }
        echo $sErrorMsg;
        echo " Selenium tests terminated.";
        $this->getMinkSession()->stop();

        exit(1);
    }

    /* --------------------- Functions for both admin and frontend -----------------------------*/

    /**
     * Opens new browser window
     *
     * @param string $sUrl url to open
     * @param bool $blClearCache clears cache before opening page
     */
    public function openNewWindow( $sUrl, $blClearCache = true )
    {
        $this->_sSelectedFrame = 'relative=top';
        try {
            $this->selectWindow(null);
            if ($this->_sDriver == 'selenium') {
                $this->windowMaximize(null);
            }
        } catch(\Behat\Mink\Exception\Exception $e) {
            // Do nothing if methods not implemented, for example with headless driver.
        }

        if ( $blClearCache ) {
            $this->clearTemp();
        }

        try {
            $this->open( $sUrl );
        } catch ( Exception $e ) {
            usleep(500000);
            $this->open( $sUrl );
        }
        $this->checkForErrors();
    }

    /**
     * $_oTranslator setter
     *
     * @param object $oTranslator
     */
    public static function setTranslator( $oTranslator )
    {
        self::$_oTranslator = $oTranslator;
    }

    /**
     * $_oTranslator getter
     *
     * @return oxTranslator
     */
    public static function getTranslator()
    {
        if ( is_null( self::$_oTranslator ) ) {
            self::$_oTranslator = new oxTranslator();
        }
        return self::$_oTranslator;
    }

    /**
     * Calls oxTranslator and tries to translate a string
     * throws fail if string is found, but can't be translated
     *
     * @param $sString
     *
     * @return null
     */
    public static function translate( $sString )
    {
        $sString = self::getTranslator()->translate( $sString );
        $aUntranslated = self::getTranslator()->getUntranslated();
        if ( count( $aUntranslated ) > 0 ) {
            self::fail( "Untranslated strings: " . implode( ', ', $aUntranslated ) );
        }
        return $sString;
    }

    /* --------------------- eShop frontend side only functions ---------------------- */

    /**
     * opens shop frontend and runs checkForErrors().
     *
     * @param bool $blForceMainShop opens main shop even if SubShop is being tested
     * @param bool $blClearCache whether to clear cache
     * @return null
     */
    public function openShop( $blForceMainShop = false, $blClearCache = false )
    {
        $this->openNewWindow( shopURL, $blClearCache );

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
        return $this->getMinkSession()->getPage();
    }

    /**
     * Selects shop language in frontend.
     *
     * @param string $sLanguage language title.
     */
    public function switchLanguage( $sLanguage )
    {
        $this->click("languageTrigger");
        $this->waitForItemAppear("languages");
        $this->clickAndWait("//ul[@id='languages']//li/a/span[text()='".$sLanguage."']");
        $this->getTranslator()->setLanguageByName( $sLanguage );
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

    public function logIn( $sUserName, $sPassword, $aAdditionalParams = array(), $sShopId = null )
    {
        $aParams['cl'] = 'account';
        $aParams['fnc'] = 'login';
        $aParams['lgn_usr'] = $sUserName;
        $aParams['lgn_pwd'] = $sPassword;

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
        $this->clearCache();
        $this->openShop();
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
            $sLink = "Display cart";
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
     * @param string $sSelectedCombination Waits for selected combination to change.
     * @return null
     */
    public function selectVariant($elementId, $elementNr, $itemValue, $sSelectedCombination = '')
    {
        $this->assertElementPresent($elementId);
        $this->assertFalse($this->isVisible("//div[@id='".$elementId."']/div[".$elementNr."]//ul"));
        $this->click("//div[@id='".$elementId."']/div[".$elementNr."]//p");

        $this->waitForItemAppear("//div[@id='".$elementId."']/div[".$elementNr."]//ul");
        $this->click("//div[@id='".$elementId."']/div[".$elementNr."]//ul//a[text()='".$itemValue."']");

        if (!empty($sSelectedCombination)) {
            $this->waitForText("%SELECTED_COMBINATION%: $sSelectedCombination");
        }
    }

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

        if ( OXID_VERSION_PE_CE ) :
            $this->openTab('Interface');
        endif;

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

        $this->waitForFrameToLoad('basefrm', 5000, true);
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
    public function logoutAdmin( $sLocator = "link=Log out" )
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
        $this->checkForErrors();
        $this->click("link=OXID eFire");
        $this->click("link=Shop connector");

        $this->waitForFrameToLoad('basefrm', 5000, true);

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
     * @param string $sFrame        Name of the frame.
     * @param bool $blForceReselect Switches frame even if it is currently selected
     * @param bool $blFollowPath    If path to frame is defined, it selects all frames in path
     */
    public function frame( $sFrame, $blForceReselect = false, $blFollowPath = true )
    {
        if ( !$blForceReselect && $this->getSelectedFrame() == $sFrame ) {
            return;
        }

        if ( $blFollowPath && $this->_aFramePaths[$sFrame] ) {
            $aPath = explode( "/", $this->_aFramePaths[$sFrame] );
            $this->_selectFrameByPath( $aPath );
        } else {
            $this->selectFrame( $sFrame );
        }

        $this->checkForErrors();
    }

    /**
     * Returns given frame parent. If none selected - returns current frame parent
     *
     * @param string $sFrame
     * @return string real frame name
     */
    public function selectParentFrame( $sFrame = null )
    {
        $sFrame = $sFrame? $sFrame : $this->getSelectedFrame();

        if ( $this->_aFramePaths[$sFrame] ) {
            $aPath = explode( "/", $this->_aFramePaths[$sFrame] );
            $sFrame = array_pop( $aPath );
            $this->_selectFrameByPath( $aPath );
        } else {
            $this->selectFrame( "relative=top" );
        }

        return $sFrame;
    }

    /**
     * Clicks new item button
     *
     * @param string $sButtonSelector
     */
    public function clickCreateNewItem( $sButtonSelector = "btn.new" )
    {
        $this->frame( 'edit' );
        $this->click( $sButtonSelector );
        $this->waitForFrameToLoad( 'list', 5000 );
        $this->waitForFrameToLoad( 'edit', 5000, true );
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
        $sItemName = $this->translate( $sItemName );
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

    public static function assertEquals($expected, $actual, $message = '', $delta = 0, $maxDepth = 10, $canonicalizeEol = false, $ignoreCase = false)
    {
        $expected = self::translate( $expected );
        $actual = self::translate( $actual );

        $expected = self::_clearString( $expected );
        $sMessage = "'$expected' != '$actual' with message: ". $message;

        parent::assertEquals( $expected, $actual, $sMessage, $delta, $maxDepth, $canonicalizeEol, $ignoreCase );
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

    /* ------------------------ Selenium API related functions, override functions ---------------------- */

    /**
     * Opens new window in popUp
     *
     * @param string $sUrl
     * @param string $sId
     * @return null
     */
    public function openWindow( $sUrl, $sId )
    {
        parent::openWindow( $sUrl, $sId );
        $this->selectWindow($sId);
        $this->waitForPageToLoad( 10000 );
    }

    /**
     * Clicks link/button and waits till page will be loaded. then checks for errors.
     * recommended to use in frontend. use in admin only, if this click wont relode frames.
     *
     * @param string $locator link/button locator in the page.
     * @param int $iSeconds   How much time to wait for element.
     * @return null
     */
    public function clickAndWait( $locator, $iSeconds = 10 )
    {
        $locator = $this->translate( $locator );
        if ( $this->getSelectedFrame() != 'relative=top' ) {
            $this->clickAndWaitFrame( $locator );
            return;
        }

        $this->click( $locator );
        try {
            $this->waitForPageToLoad( $iSeconds * 1000 );
        } catch ( Exception $e ) {}

        $this->checkForErrors();
    }

    /**
     * Selects label in select list and waits till page will be loaded. then checks for errors.
     * recommended to use in frontend. use in admin only, if this select wont reload frames.
     *
     * @param string $locator select list locator.
     * @param string $selection   option to select.
     * @param string $element element locator for additional check if page is fully loaded (optional).
     * @return null
     */
    public function selectAndWait( $locator, $selection, $element = null )
    {
        if ( $this->getSelectedFrame() != 'relative=top' ) {
            $this->selectAndWaitFrame( $locator, $selection );
            return;
        }

        $this->waitForElement($locator);
        $this->select($locator, $selection);
        $this->waitForPageToLoad( 10000 );

        if ($element) {
            $this->waitForElement($element);
        }
        $this->checkForErrors();
    }

    /**
     * selects element and waits till needed frame will be loaded. same frame as before will be selected.
     *
     * @param string $locator select list locator.
     * @param string $frame   frame which should be also loaded (this frame will be loaded after current frame is loaded).
     * @return null
     */
    public function clickAndWaitFrame( $locator, $frame = '' )
    {
        $this->click( $locator );
        $this->waitForFrameAfterAction( $frame );
        $this->checkForErrors();
    }

    /**
     * selects element and waits till needed frame will be loaded. same frame as before will be selected.
     *
     * @param string $locator   select list locator.
     * @param string $selection option to select.
     * @param string $frame     frame which should be also loaded (this frame will be loaded after current frame is loaded).
     * @return null
     */
    public function selectAndWaitFrame( $locator, $selection, $frame = '' )
    {
        $this->waitForElement($locator);
        $this->select($locator, $selection);
        $this->waitForFrameAfterAction( $frame );
        $this->checkForErrors();
    }

    /**
     * Clicks button and confirms dialog.
     * JavaScript confirmations will NOT pop up a visible dialog.
     * By default, the confirm action is as manually clicking OK.
     * This can be changed by prior execution of the chooseCancelOnNextConfirmation command.
     * If an confirmation is generated but you do not get/verify it, the next Selenium action will fail.
     *
     * @param string $locator locator for delete button.
     * @param string $frame   frame which should be also loaded (this frame will be loaded after current frame is loaded).
     * @return null
     */
    public function clickAndConfirm($locator, $frame="")
    {
        $this->waitForElement($locator);
        $this->click($locator);
        $this->getConfirmation();
        $this->waitForFrameAfterAction($frame);

        $this->checkForErrors();
    }

    /**
     * Waits for frames to load after action.
     * If $sFrame is passed, will wait for this frame after main frame was loaded
     *
     * @param string $sFrame
     */
    protected function waitForFrameAfterAction( $sFrame = '' )
    {
        $sSelectedFrame = $this->getSelectedFrame();
        $sFrame = $sFrame? $sFrame : $sSelectedFrame;

        if ( $sFrame && $sSelectedFrame != $sFrame ) {
            $this->waitForFrameToLoad( $sSelectedFrame, 5000, true );
        }
        $this->waitForFrameToLoad( $sFrame, 5000, true );
    }

    /**
     * Waits till element will appear in page (only IF such element DID NOT EXIST BEFORE).
     *
     * @param string $sLocator element locator.
     * @param int $iSeconds   How much time to wait for element.
     * @return null
     */
    public function waitForElement( $sLocator, $iSeconds = 10 )
    {
        $this->_waitForAppear( 'isElementPresent', $sLocator, $iSeconds );
    }

    /**
     * Waits till element will appear in page (only IF such element DID NOT EXIST BEFORE).
     *
     * @param string $sLocator element locator.
     * @param int $iTimeToWait   How much time to wait for element.
     * @return null
     */
    public function waitForEditable( $sLocator, $iTimeToWait = 10 )
    {
        $this->_waitForAppear( 'isEditable', $sLocator, $iTimeToWait );
    }

    /**
     * Waits for element to show up (only IF such element ALREADY EXIST AS HIDDEN AND WILL BE SHOWN AS VISIBLE).
     *
     * @param string $sLocator element locator.
     * @param int $iTimeToWait time to wait for element
     * @return null
     */
    public function waitForItemAppear( $sLocator, $iTimeToWait = 10 )
    {
        $sLocator = $this->translate( $sLocator );
        $this->_waitForAppear( 'isElementPresent', $sLocator, $iTimeToWait );
        $this->_waitForAppear( 'isVisible', $sLocator, $iTimeToWait );
    }

    /**
     * Waits for element to disappear (only IF such element WILL BE MARKED AS NOT VISIBLE).
     *
     * @param string $sLocator element locator.
     * @param int $iTimeToWait time to wait for element
     * @return null
     */
    public function waitForItemDisappear( $sLocator, $iTimeToWait = 10 )
    {
        $sLocator = $this->translate( $sLocator );
        $this->_waitForDisappear( 'isVisible', $sLocator, $iTimeToWait );
    }

    /**
     * Waits till text will appear in page. If array is passed, waits for any of texts in array to appear.
     *
     * @param string|array $mTextMsg If Array of Messages is passed, returns when either of given texts if found
     * @param bool $printSource print source (default false).
     * @param int $iTimeToWait timeout (default 10).
     * @return null
     */
    public function waitForText( $mTextMsg, $printSource = false, $iTimeToWait = 10 )
    {
        $mTextMsg = $this->translate( $mTextMsg );
        $this->_waitForAppear( 'isTextPresent', $mTextMsg, $iTimeToWait );
    }

    /**
     * Waits till text will disappear from page.
     *
     * @param string $textLine text.
     * @param int $iTimeToWait timeout (default 10).
     * @return null
     */
    public function waitForTextDisappear( $textLine, $iTimeToWait = 10 )
    {
        $textLine = $this->translate( $textLine );
        $this->_waitForDisappear( 'isTextPresent', $textLine, $iTimeToWait );
    }

    /**
     * Waits for specified method with given parameter to return true.
     * If multiple parameters is passed, waits till true is returned on any of them.
     *
     * @param string $sMethod
     * @param string|array $mParams
     * @param int $sTimeToWait
     * @param bool $blIgnoreResult
     */
    protected function _waitForAppear( $sMethod, $mParams, $sTimeToWait = 10, $blIgnoreResult = false)
    {
        $aParams = is_array( $mParams )? $mParams : array( $mParams );

        $sTimeToWait = $sTimeToWait * 2 * $this->_iWaitTimeMultiplier;
        $blResetFrame = true;
        for ($iSecond = 0; $iSecond <= $sTimeToWait; $iSecond++) {
            if ( $this->_isElementAppeared( $sMethod, $aParams ) ) {
                return;
            }
            if ( $blResetFrame && $iSecond >= $sTimeToWait/2 ) {
                if ( $this->getSelectedWindow() == null ) {
                    $this->frame($this->getSelectedFrame(), true);
                }
                $blResetFrame = false;
            } else if (!$blIgnoreResult && $iSecond >= $sTimeToWait ) {
                $sMessage = "Timeout waiting for '".implode(' | ', $aParams)."' ";
                $this->fail( $sMessage );
            }
            usleep(500000);
        }
    }

    /**
     * @param string $sMethod
     * @param array $aParams
     * @return bool
     */
    protected function _isElementAppeared( $sMethod, $aParams )
    {
        foreach ( $aParams as $sParam ) {
            try {
                if ( $this->$sMethod( $sParam ) ) {
                    return true;
                }
            } catch (Exception $e) {}
        }
        return false;
    }

    /**
     * Waits for specified method with given message to return true.
     *
     * @param string $sMethod
     * @param string $sMessage
     * @param int $sTimeToWait
     */
    protected function _waitForDisappear( $sMethod, $sMessage, $sTimeToWait = 30)
    {
        $sTimeToWait = $sTimeToWait * 2 * $this->_iWaitTimeMultiplier;
        for ($iSecond = 0; $iSecond <= $sTimeToWait; $iSecond++) {
            try {
                if ( ! $this->$sMethod( $sMessage ) ) {
                    return;
                }
            } catch (Exception $e) {}

            if ($iSecond >= $sTimeToWait ) {
                $this->fail( "Timeout waiting for '$sMessage'" );
            }
            usleep(500000);
        }
    }

    /**
     * Overrides original method - waits for element before checking for text
     *
     * @param string $sLocator text to be searched
     * @return bool
     */
    public function getText( $sLocator )
    {
        $sLocator = $this->translate( $sLocator );
        return parent::getText( $sLocator );
    }

    /**
     * selects element and waits till needed frame will be loaded. same frame as before will be selected.
     *
     * @param string $sLocator select list locator.
     * @return null
     */
    public function click( $sLocator )
    {
        $sLocator = $this->translate( $sLocator );
        return parent::click( $sLocator );
    }

    /**
     * @param $sSelector
     * @param $sOptionSelector
     */
    public function select( $sSelector, $sOptionSelector )
    {
        $sSelector = $this->translate( $sSelector );
        $sOptionSelector = $this->translate( $sOptionSelector );
        return parent::select( $sSelector, $sOptionSelector );
    }

    /**
     * Checks if element is visible. If element is not found, waits for it to appear and checks again.
     *
     * @param string $sLocator
     * @return bool
     */
    public function isVisible( $sLocator )
    {
        $sLocator = $this->translate( $sLocator );
        return parent::isVisible( $sLocator );
    }

    /**
     * Skip test code until given date.
     *
     * @param string $sDate Date string in format 'Y-m-d'.
     *
     * @return bool
     */
    public function skipTestBlockUntil( $sDate )
    {
        $blSkip = false;
        $oDate = DateTime::createFromFormat( 'Y-m-d', $sDate );
        if ( time() >= $oDate->getTimestamp() ) {
            $blSkip = true;
        }
        return $blSkip;
    }

    /**
     * Mark the test as skipped until given date.
     * Wrapper function for PHPUnit_Framework_Assert::markTestSkipped.
     *
     * @param string $sDate    Date string in format 'Y-m-d'.
     * @param string $sMessage Message.
     *
     * @throws PHPUnit_Framework_SkippedTestError
     */
    public function markTestSkippedUntil( $sDate, $sMessage = '' )
    {
        $oDate = DateTime::createFromFormat( 'Y-m-d', $sDate );
        if ( time() < $oDate->getTimestamp() ) {
            $this->markTestSkipped( $sMessage );
        }
    }

    /**
     * Asserts that element is present.
     *
     * @param string $sLocator element locator
     * @param string $sMessage fail message
     * @return void
     */
    public function assertElementPresent( $sLocator, $sMessage = '' )
    {
        $sLocator = $this->translate( $sLocator );
        $sFailMessage = "Element $sLocator was not found! " . $sMessage;
        $this->assertTrue( $this->isElementPresent( $sLocator ), $sFailMessage );
    }

    /**
     * Asserts that element is not present.
     *
     * @param string $sLocator element locator
     * @param string $sMessage fail message
     * @return void
     */
    public function assertElementNotPresent( $sLocator, $sMessage = '' )
    {
        $sFailMessage = "Element $sLocator was found though it should not be present! " . $sMessage;
        $this->assertFalse( $this->isElementPresent( $sLocator ), $sFailMessage );
    }

    /**
     * Asserts that text is present.
     *
     * @param string $sText text to search
     * @param string $sMessage fail message
     * @return void
     */
    public function assertTextPresent( $sText, $sMessage = '' )
    {
        $sText = $this->translate( $sText );
        $sFailMessage = "Text '$sText' was not found! " . $sMessage;
        $this->assertTrue( $this->isTextPresent( $sText ), $sFailMessage );
    }

    /**
     * Asserts that text is not present.
     *
     * @param string $sText text to search
     * @param string $sMessage fail message
     * @return void
     */
    public function assertTextNotPresent( $sText, $sMessage = '' )
    {
        $sText = $this->translate( $sText );
        $sFailMessage = "Text '$sText' was not found! " . $sMessage;
        $this->assertFalse( $this->isTextPresent( $sText ), $sFailMessage );
    }

    /**
     * Asserts that element is visible.
     *
     * @param string $sLocator element to search
     * @param string $sMessage fail message
     * @return void
     */
    public function assertElementVisible( $sLocator, $sMessage = '' )
    {
        $sFailMessage = "Element '$sLocator' is not visible! " . $sMessage;
        $this->assertTrue( $this->isVisible( $sLocator ), $sFailMessage );
    }

    /**
     * Asserts that element is not visible.
     *
     * @param string $sLocator element to search
     * @param string $sMessage fail message
     * @return void
     */
    public function assertElementNotVisible( $sLocator, $sMessage = '' )
    {
        $sFailMessage = "Element '$sLocator' should not be visible! " . $sMessage;
        $this->assertFalse( $this->isVisible( $sLocator ), $sFailMessage );
    }

    /**
     * Asserts that element is editable.
     *
     * @param string $sLocator element to search
     * @param string $sMessage fail message
     * @return void
     */
    public function assertElementEditable( $sLocator, $sMessage = '' )
    {
        $sFailMessage = "Element '$sLocator' is not editable! " . $sMessage;
        $this->assertTrue( $this->isEditable( $sLocator ), $sFailMessage );
    }

    /**
     * Asserts that element is not editable.
     *
     * @param string $sLocator element to search
     * @param string $sMessage fail message
     * @return void
     */
    public function assertElementNotEditable( $sLocator, $sMessage = '' )
    {
        $sFailMessage = "Element '$sLocator' should not be editable! " . $sMessage;
        $this->assertFalse( $this->isEditable( $sLocator ), $sFailMessage );
    }

    /* ------------------------ Mink related functions ---------------------------------- */

    /**
     * @param string $sDriver
     */
    public function startMinkSession( $sDriver = null )
    {
        $sDriver = $sDriver ? $sDriver : $this->_sDriver;
        $this->_oMinkSession = new \Behat\Mink\Session( $this->_getMinkDriver( $sDriver ) );
        $this->_oMinkSession->start();
    }

    /**
     * @param $sDriver
     */
    public function switchMinkSession( $sDriver )
    {
        $this->getMinkSession()->stop();
        $this->startMinkSession( $sDriver );
    }

    /**
     * @return \Behat\Mink\Session
     */
    public function getMinkSession()
    {
        if ( is_null( $this->_oMinkSession ) ) {
            $this->startMinkSession();
        }
        return $this->_oMinkSession;
    }

    /**
     * @param string $sDriver driver name
     *
     * @return \Behat\Mink\Driver\DriverInterface
     */
    protected function _getMinkDriver( $sDriver = '' )
    {
        switch ( $sDriver ) {
            case 'selenium2':
                /*$aDesiredCapabilities = array(
                    'browserName'       => 'firefox',
                    'browserVersion'    => '10',
                    'browser'           => '/opt/firefox10',
                );*/
                $oDriver = new \Behat\Mink\Driver\Selenium2Driver( browserName );
                break;
            case 'sahi':
                $oDriver = new \Behat\Mink\Driver\SahiDriver( browserName, $this->_getClient() );
                break;
            case 'goutte':
                $aClientOptions = array();
                $oGoutteClient = new \Behat\Mink\Driver\Goutte\Client();
                $oGoutteClient->setClient(new \Guzzle\Http\Client('', $aClientOptions));
                $oDriver = new \Behat\Mink\Driver\GoutteDriver($oGoutteClient);
                break;
            case 'zombie':
                $oDriver = new \Behat\Mink\Driver\ZombieDriver();
                break;
            case 'selenium':
            default:
                $oDriver = new \Behat\Mink\Driver\SeleniumDriver( browserName, shopURL, $this->_getClient() );
                break;
        }

        return $oDriver;
    }

    /**
     * @return \Selenium\Client
     */
    protected function _getClient()
    {
        if ( is_null( $this->_oClient ) ) {
            $this->_oClient = new \Selenium\Client(hostUrl, '4444');
        }

        return $this->_oClient;
    }

//----------------------------- Tests BoilerPlate related functions ------------------------------------

    /**
     * Creates a dump of the current database, stored in the file '/tmp/tmp_db_dump'
     * the dump includes the data and sql insert statements.
     *
     * @param string $sTmpPrefix temp file name.
     * @throws Exception on error while dumping.
     * @return null
     */
    public function dumpDB( $sTmpPrefix = null )
    {
        $time = microtime ( true );

        $oShopPreparation = new oxShopPreparation();
        $oShopPreparation->setTemporaryFolder( oxCCTempDir );
        $oShopPreparation->dumpDB( $sTmpPrefix );

        echo( "db Dumptime: ".(microtime (true)-$time)."\n" );
    }

    /**
     * Checks which tables of the db changed and then restores these tables.
     *
     * Uses dump file '/tmp/tmp_db_dump' for comparison and restoring.
     *
     * @param string $sTmpPrefix temp file name
     * @throws Exception on error while restoring db
     * @return null
     */
    public function restoreDB( $sTmpPrefix = null )
    {
        $oShopPreparation = new oxShopPreparation();
        $oShopPreparation->setTemporaryFolder( oxCCTempDir );
        $oShopPreparation->restoreDB( $sTmpPrefix );
    }

    /**
     * Adds some demo data to database.
     *
     * @param string $demo DemoData file name
     * @throws Exception
     * @return null
     */
    public function addDemoData( $demo )
    {
        if ( filesize( $demo ) ) {

            $oShopPreparation = new oxShopPreparation();
            $oShopPreparation->import( $demo );

            if ( OXID_VERSION_EE ) :
                oxDb::getDb()->execute( "delete from oxcache" );
            endif;
        }
    }

    /**
     * executes given sql. for EE version cash is also cleared.
     * @param string $sql  sql line.
     */
    public function executeSql($sql)
    {
        oxDb::getDb()->execute($sql);
        if ( OXID_VERSION_EE ) :
            oxDb::getDb()->execute( "delete from oxcache" );
        endif;
    }

    /**
     * Call shop seleniums connector to execute code in shop.
     * @example call to update information to database.
     *
     * @param string $sClass class name.
     * @param string $sFnc function name.
     * @param string $sId id of object.
     * @param array  $aClassParams params to set to object.
     * @param array  $aFunctionParams params to set to object.
     * @param string $sShopId object shop id.
     * @param string $sLang object shop id.
     *
     * @return mixed
     */
    public function callShopSC($sClass, $sFnc, $sId = null, $aClassParams = array(), $aFunctionParams = array(), $sShopId = null, $sLang = 'en')
    {
        $oServiceCaller = new oxServiceCaller();
        $oServiceCaller->setParameter('cl', $sClass);
        $oServiceCaller->setParameter('fnc', $sFnc);
        $oServiceCaller->setParameter('oxid', $sId);
        $oServiceCaller->setParameter('lang', $sLang);

        $oServiceCaller->setParameter('classparams', $aClassParams);
        $oServiceCaller->setParameter('functionparams', $aFunctionParams);

        $mResponse = $oServiceCaller->callService('ShopObjectConstructor', $sShopId);

        if (is_string($mResponse) && strpos($mResponse, 'EXCEPTION:') === 0) {
            $this->fail("Exception caught calling ShopObjectConstructor with message: '$mResponse'");
        }

        return $mResponse;
    }

    /**
     * @return oxObjectValidator
     */
    public function getObjectValidator()
    {
        if (!$this->_oValidator) {
            $this->_oValidator = new oxObjectValidator();
        }

        return $this->_oValidator;
    }

    /**
     * Returns data value from file
     *
     * @param $sVarName
     * @param $sFilePath
     * @return string
     */
    public function getArrayValueFromFile( $sVarName, $sFilePath )
    {
        $aData = null;
        if ( file_exists( $sFilePath ) ) {
            $aData = include $sFilePath;
        }

        return $aData[$sVarName];
    }

//----------------------------- Other functions, PHPUnit fixes, etc ------------------------------------

    /**
     * Return main shop number.
     * To use to form link to main shop and etc.
     *
     * @return string
     */
    public function getShopVersionNumber()
    {
        return '5';
    }

    /**
     * tests if none of php possible errors are displayed into shop frontend page.
     *
     * @return null
     */
    public function checkForErrors()
    {
        $sHTML = $this->getHtmlSource();
        $aErrorTexts = array(
            "Warning: " => "PHP Warning is in the page",
            "ADODB_Exception" => "ADODB Exception is in the page",
            "Fatal error: " => "PHP Fatal error is in the page",
            "Catchable fatal error: " => " Catchable fatal error is in the page",
            "Notice: " => "PHP Notice is in the page",
            "exception '" => "Uncaught exception is in the page",
            "does not exist or is not accessible!" => "Warning about not existing function is in the page ",
            "ERROR: Tran" => "Missing translation for constant (ERROR: Translation for...)",
            "EXCEPTION_" => "Exception - component not found (EXCEPTION_)",
            "oxException" => "Exception is in page"
        );

        foreach ( $aErrorTexts as $sError => $sMessage ) {
            if ( strpos($sHTML, $sError) !== false ) {
                $this->fail($sMessage);
            }
        }
    }

    /**
     * Returns clean heading text without any additional info as rss labels and so..
     *
     * @param string $element path to element.
     * @return string
     */
    public function getHeadingText($element)
    {
        $text = $this->getText($element);
        if ($this->isElementPresent($element."/a")) {
            $search = $this->getText($element."/a");
            $text = str_replace($search, "", $text);
        }
        return trim($text);
    }

    /**
     * Removes \n signs and it leading spaces from string. keeps only single space in the ends of each row.
     *
     * @param string $sLine not formatted string (with spaces and \n signs).
     * @return string formatted string with single spaces and no \n signs.
     */
    public function clearString( $sLine )
    {
        return trim(preg_replace("/[ \t\r\n]+/", ' ', $sLine));
    }

    /**
     * Clears shop cache
     */
    public function clearCache()
    {
        $this->clearTemp();
        $this->clearCookies();
    }

    /**
     * Clears shop cache, (with _cc file)
     *
     * @return null
     */
    public function clearCookies()
    {
        $this->open( preg_replace('/:{1}\d{1,}/','',shopURL). '/' . "_cc.php" );
        $this->getTranslator()->setLanguage( 1 );
    }

    /**
     * Clears shop cache, (with _cc file)
     *
     * @return null
     */
    public function clearTemp()
    {
        $oServiceCaller = new oxServiceCaller();
        $oServiceCaller->callService('ClearCache');
    }

    /**
     * Logs method loading times
     *
     * @param string $sMethod
     * @param int $iTime
     */
    public function addToLog($sMethod, $iTime)
    {
        if ( !$this->_blEnableLog ) {
            return;
        }
        $sLogFile = oxPATH . '/perf_logs.txt';
        if ( file_exists( $sLogFile ) ) {
            $aData = unserialize(file_get_contents($sLogFile));
        } else {
            $aData = array();
        }
        if ( !$aData[$sMethod] ) {
            $aData[$sMethod] = array( 'time' => 0, 'count' => 0, 'messages' => array() );
        }
        $aData[$sMethod]['time'] += intval($iTime*10000);
        $aData[$sMethod]['count']++;

        file_put_contents( $sLogFile, serialize($aData) );
    }

    /**
     * Searches articles.
     *
     * @param $sSearchParameter
     * @param null $sShopId
     */
    public function searchArticle( $sSearchParameter, $sShopId = null )
    {
        $aParams['cl'] = 'search';
        $aParams['searchparam'] = $sSearchParameter;

        $this->openNewWindow( $this->_getShopUrl( $aParams, $sShopId ), false );
    }

    /**
     * Clear spaces and new lines as Mink do.
     * @param $sToClear
     * @return mixed
     */
    protected static function _clearString( $sToClear )
    {
        $sToClear = preg_replace( "/[ \n]+/", " ", $sToClear );
        return $sToClear;
    }

    /**
     * Fix for showing stack trace with phpunit 3.6 and later
     *
     * @param Exception $e
     * @throws PHPUnit_Framework_Error
     * @throws PHPUnit_Framework_IncompleteTestError
     * @throws PHPUnit_Framework_SkippedTestError
     */
    protected function onNotSuccessfulTest(Exception $e)
    {
        if ( $this->_isInternalServerError() ) {
            $this->runTest();
            return;
        }
        try {
            parent::onNotSuccessfulTest($e);
        }
        catch (PHPUnit_Framework_IncompleteTestError $e) {
            throw $e;
        }
        catch (PHPUnit_Framework_SkippedTestError $e) {
            throw $e;
        }

        catch (Exception $oParentException) {
            $aFilteredTrace = PHPUnit_Util_Filter::getFilteredStacktrace( $e, false );
            $sErrorMessage = $this->_getScreenShot();
            $sErrorMessage .= $oParentException->getMessage();
            $sErrorMessage .= "\nSelected Frame: '".$this->getSelectedFrame(). "' in window '".$this->getSelectedWindow()."' ";
            $sErrorMessage .= "\n\n".$this->_formTrace( $aFilteredTrace );

            $oTrace = $oParentException;
            if ( version_compare(PHPUnit_Runner_Version::id(), '3.7', '<') ) {
                $oTrace = $oParentException->getTrace();
            }
            throw new PHPUnit_Framework_Error( $sErrorMessage, $oParentException->getCode(), $oParentException->getFile(), $oParentException->getLine(), $oTrace);
        }
    }

    /**
     * Take a screenshot and return information about it.
     * Return an empty string if the screenshotPath and screenshotUrl
     * properties are empty.
     * Issue #88.
     *
     * @access protected
     * @return string
     */
    protected function _getScreenShot()
    {
        $sPath = $this->_getScreenShotPath();
        if ( $sPath ) {
            $sFileName = basename( __FILE__ ) . '_' . $this->getName( false ) . '_' . time() . '.png';

            $this->getScreenShot( $sPath . $sFileName );

            return 'Screenshot: ' . SELENIUM_SCREENSHOTS_URL . '/' . $sFileName . "\n";
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    protected function _getScreenShotPath()
    {
        $sPath = '';

        if ( SELENIUM_SCREENSHOTS_PATH != '' ) {
            $sPath = SELENIUM_SCREENSHOTS_PATH;

            if ( !in_array( substr( $sPath, strlen( $sPath ) - 1, 1 ), array( "/", "\\" ) ) ) {
                $sPath .= DIRECTORY_SEPARATOR;
            }
        }

        return $sPath;
    }

    /**
     * Checks whether any currently opened windows contains internal server error
     *
     * @return bool
     */
    protected function _isInternalServerError()
    {
        $sHTML = $this->getHtmlSource();
        if( strpos($sHTML, '500 Internal Server Error') !== false ) {
            return true;
        }

        return false;
    }

    /**
     * Forms trace message from given array.
     *
     * @param array $aTrace
     * @return string
     */
    protected function _formTrace( $aTrace )
    {
        if ( !is_array($aTrace) ) {
            return $aTrace;
        }
        $aSkipMethods = array( "main" );
        $sResult = '';
        $aReversedTrace = array_reverse($aTrace);
        foreach ( $aReversedTrace as $aCall ) {
            if ( strpos( $aCall['file'], '/usr' ) === 0 || strpos( $aCall['file'], '/tmp') === 0 ) {
                continue;
            }
            $sResult .= ( !in_array( $aCall['function'], $aSkipMethods ) )? $this->_parseTraceCall( $aCall ) : '';
        }
        return $sResult;
    }

    /**
     * Forms readable trace line from given trace call array
     *
     * @param array $aTraceCall
     * @return string
     */
    protected function _parseTraceCall( $aTraceCall )
    {
        return sprintf(
            "%s:%s (%s)\n",
            $aTraceCall['file'],
            (isset($aTraceCall['line']) ? $aTraceCall['line'] : '?'),
            $aTraceCall['function']
        );
    }

    /**
     * Forms shop url with given parameters
     *
     * @param array $aParams
     * @param null $sShopId
     * @return string
     */
    protected function _getShopUrl( $aParams = array(), $sShopId = null )
    {
        if ( $sShopId && oxSHOPID != 'oxbaseshop' ) {
            $aParams['shp'] = $sShopId;
        } elseif ( isSUBSHOP ) {
            $aParams['shp'] = oxSHOPID;
        }

        return shopURL."index.php?" . http_build_query( $aParams );
    }

    /**
     * @param $aPath
     */
    protected function _selectFrameByPath( $aPath )
    {
        $this->selectFrame( 'relative=top' );
        foreach ( $aPath as $sFrame ) {
            if ( $sFrame != 'list' || $this->isElementPresent( 'list' ) ) {
                $this->selectFrame( $sFrame );
            }
        }
    }

    /**
     * Override to run the test and assert its state.
     *
     * @return mixed
     * @throws RuntimeException
     */
    protected function runTest()
    {
        try {
            $testResult = parent::runTest();
        } catch( Exception $e ){
            $this->fail( $e->getMessage() );
        }

        return $testResult;
    }
}