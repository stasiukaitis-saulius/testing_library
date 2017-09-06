<?php
/**
 * This file is part of OXID eSales Testing Library.
 *
 * OXID eSales Testing Library is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales Testing Library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales Testing Library. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 */

namespace OxidEsales\TestingLibrary\Services\ShopInstaller;

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Edition\EditionPathProvider;
use OxidEsales\Eshop\Core\Edition\EditionRootPathProvider;
use OxidEsales\Eshop\Core\Edition\EditionSelector;
use OxidEsales\EshopCommunity\Setup\Core;
use OxidEsales\TestingLibrary\Services\Library\Cache;
use OxidEsales\TestingLibrary\Services\Library\DatabaseHandler;
use OxidEsales\TestingLibrary\Services\Library\Request;
use OxidEsales\TestingLibrary\Services\Library\CliExecutor;
use OxidEsales\EshopProfessional\Core\Serial;
use OxidEsales\TestingLibrary\Services\NoBootstrapNeededService;
use OxidEsales\TestingLibrary\TestConfig;

/**
 * Class for OXID eShop installation.
 */
class ShopInstaller extends NoBootstrapNeededService
{
    /** @var DatabaseHandler */
    private $dbHandler;

    /** @var EditionPathProvider */
    private $editionPathProvider;

    /**
     * Starts installation of the OXID eShop.
     *
     * @param Request $request
     *
     * @throws \Exception
     */
    public function init($request)
    {
        $this->shopConfig = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class);
        $this->dbHandler = new DatabaseHandler($this->shopConfig);

        if (!class_exists('\OxidEsales\EshopCommunity\Setup\Setup')) {
            throw new \Exception("OXID eShop Setup directory has to be present!");
        }

        $serialNumber = $request->getParameter('serial', false);
        $serialNumber = $serialNumber ? $serialNumber : $this->getDefaultSerial();

        $this->setupDatabase();

        if ($tempDir = $request->getParameter('tempDirectory')) {
            $this->insertConfigValue('string', 'sCompileDir', $tempDir);
        }
        $this->insertConfigValue('int', 'sOnlineLicenseNextCheckTime', time() + 25920000);

        if ($request->getParameter('addDemoData', false)) {
            $this->insertDemoData();
        }

        if ($request->getParameter('international', false)) {
            $this->convertToInternational();
        }

        $this->setConfigurationParameters();

        $this->setSerialNumber($serialNumber);

        $config = $this->getShopConfig();
        $default = property_exists($config, 'turnOnVarnish') ? $config->turnOnVarnish : false;
        if ($request->getParameter('turnOnVarnish', $default)) {
            $this->turnVarnishOn();
        }

        $cache = new Cache();
        $cache->clearTemporaryDirectory();
    }

    /**
     * Sets up database.
     */
    public function setupDatabase()
    {
        $dbHandler = $this->getDbHandler();

        $dbHandler->getDbConnection()->exec('drop database `' . $dbHandler->getDbName() . '`');
        $dbHandler->getDbConnection()->exec('create database `' . $dbHandler->getDbName() . '` collate ' . $dbHandler->getCharsetMode() . '_general_ci');

        $baseEditionPathProvider = new EditionPathProvider(new EditionRootPathProvider(new EditionSelector(EditionSelector::COMMUNITY)));

        $dbHandler->import($baseEditionPathProvider->getDatabaseSqlDirectory() . "/database_schema.sql");
        $dbHandler->import($baseEditionPathProvider->getDatabaseSqlDirectory() . "/initial_data.sql");

        $testConfig = new TestConfig();
        $vendorDir = $testConfig->getVendorDirectory();

        CliExecutor::executeCommand('"' . $vendorDir . '/bin/oe-eshop-doctrine_migration" migrations:migrate');
        CliExecutor::executeCommand('"' . $vendorDir . '/bin/oe-eshop-db_views_regenerate"');
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    protected function detectEncodingOfFile($filename)
    {
        $encoding = '';
        $content = file_get_contents($filename);
        if ($content !== false) {
            $encoding = mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true);
        }

        return $encoding;
    }

    /**
     * Inserts test demo data to OXID eShop.
     */
    public function insertDemoData()
    {
        $testConfig = new TestConfig();
        $testDirectory = $testConfig->getEditionTestsPath($testConfig->getShopEdition());
        $this->getDbHandler()->import($testDirectory . "/Fixtures/testdemodata.sql");
    }

    /**
     * Convert OXID eShop to an international shop.
     */
    public function convertToInternational()
    {
        $this->getDbHandler()->import($this->getEditionPathProvider()->getDatabaseSqlDirectory() . "/en.sql", 'latin1');
    }

    /**
     * Inserts missing configuration parameters
     */
    public function setConfigurationParameters()
    {
        $dbHandler = $this->getDbHandler();
        $sShopId = $this->getShopId();

        $dbHandler->query("delete from oxconfig where oxvarname in ('iSetUtfMode','blLoadDynContents','sShopCountry');");
        $dbHandler->query(
            "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values " .
            "('config1', '{$sShopId}', 'iSetUtfMode',       'str',  ENCODE('0', '{$this->getConfigKey()}') )," .
            "('config2', '{$sShopId}', 'blLoadDynContents', 'bool', ENCODE('1', '{$this->getConfigKey()}') )," .
            "('config3', '{$sShopId}', 'sShopCountry',      'str',  ENCODE('de','{$this->getConfigKey()}') )"
        );
    }

    /**
     * Adds a serial number to the OXID eShop.
     *
     * @param string $serialNumber
     */
    public function setSerialNumber($serialNumber = null)
    {
        if (strtolower($this->getShopConfig()->getVar('edition')) !== strtolower(EditionSelector::COMMUNITY)
            && class_exists(Serial::class))
        {
            $dbHandler = $this->getDbHandler();

            $shopId = $this->getShopId();

            $serial = new Serial();
            $serial->setEd($this->getServiceConfig()->getShopEdition() == 'EE' ? 2 : 1);

            $serial->isValidSerial($serialNumber);

            $maxDays = $serial->getMaxDays($serialNumber);
            $maxArticles = $serial->getMaxArticles($serialNumber);
            $maxShops = $serial->getMaxShops($serialNumber);

            $dbHandler->query("update oxshops set oxserial = '{$serialNumber}'");
            $dbHandler->query("delete from oxconfig where oxvarname in ('aSerials','sTagList','IMD','IMA','IMS')");
            $dbHandler->query(
                "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values " .
                "('serial1', '{$shopId}', 'aSerials', 'arr', ENCODE('" . serialize(array($serialNumber)) . "','{$this->getConfigKey()}') )," .
                "('serial2', '{$shopId}', 'sTagList', 'str', ENCODE('" . time() . "','{$this->getConfigKey()}') )," .
                "('serial3', '{$shopId}', 'IMD',      'str', ENCODE('" . $maxDays . "','{$this->getConfigKey()}') )," .
                "('serial4', '{$shopId}', 'IMA',      'str', ENCODE('" . $maxArticles . "','{$this->getConfigKey()}') )," .
                "('serial5', '{$shopId}', 'IMS',      'str', ENCODE('" . $maxShops . "','{$this->getConfigKey()}') )"
            );
        }
    }

    /**
     * Converts OXID eShop to utf8.
     */
    public function convertToUtf()
    {
        $dbHandler = $this->getDbHandler();

        $rs = $dbHandler->query(
            "SELECT oxvarname, oxvartype, DECODE( oxvarvalue, '{$this->getConfigKey()}') AS oxvarvalue
                       FROM oxconfig
                       WHERE oxvartype IN ('str', 'arr', 'aarr')"
        );

        while ( (false !== $rs) && ($aRow = $rs->fetch())) {
            if ($aRow['oxvartype'] == 'arr' || $aRow['oxvartype'] == 'aarr') {
                $aRow['oxvarvalue'] = unserialize($aRow['oxvarvalue']);
            }
            if (!empty($aRow['oxvarvalue']) && !is_int($aRow['oxvarvalue'])) {
                $this->updateConfigValue($aRow['oxid'], $this->stringToUtf($aRow['oxvarvalue']));
            }
        }

        // Change currencies value to same as after 4.6 setup because previous encoding break it.
        $shopId = 1;

        $query = "REPLACE INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES
            ('3c4f033dfb8fd4fe692715dda19ecd28', $shopId, '', 'aCurrencies', 'arr', 0x4dbace2972e14bf2cbd3a9a45157004422e928891572b281961cdebd1e0bbafe8b2444b15f2c7b1cfcbe6e5982d87434c3b19629dacd7728776b54d7caeace68b4b05c6ddeff2df9ff89b467b14df4dcc966c504477a9eaeadd5bdfa5195a97f46768ba236d658379ae6d371bfd53acd9902de08a1fd1eeab18779b191f3e31c258a87b58b9778f5636de2fab154fc0a51a2ecc3a4867db070f85852217e9d5e9aa60507);";

        $dbHandler->query($query);
    }

    /**
     * Turns varnish on.
     */
    public function turnVarnishOn()
    {
        $dbHandler = $this->getDbHandler();

        $dbHandler->query("DELETE from oxconfig WHERE oxshopid = 1 AND oxvarname in ('iLayoutCacheLifeTime', 'blReverseProxyActive');");
        $dbHandler->query(
            "INSERT INTO oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) VALUES
              ('35863f223f91930177693956aafe69e6', 1, 'iLayoutCacheLifeTime', 'str', 0xB00FB55D),
              ('dbcfca66eed01fd43963443d35b109e0', 1, 'blReverseProxyActive',  'bool', 0x07);"
        );
    }

    /**
     * @return ConfigFile
     */
    protected function getShopConfig()
    {
        return $this->shopConfig;
    }

    /**
     * @return DatabaseHandler
     */
    protected function getDbHandler()
    {
        return $this->dbHandler;
    }

    /**
     * Returns default demo serial number for testing.
     *
     * @return string
     */
    protected function getDefaultSerial()
    {
        if ($this->getServiceConfig()->getShopEdition() != 'CE') {
            $core = new Core();
            /** @var \OxidEsales\EshopProfessional\Setup\Setup|\OxidEsales\EshopEnterprise\Setup\Setup $setup */
            $setup = $core->getInstance('Setup');
            return $setup->getDefaultSerial();
        }

        return null;
    }

    /**
     * @return EditionPathProvider
     */
    protected function getEditionPathProvider()
    {
        if (is_null($this->editionPathProvider)) {
            $editionPathSelector = new EditionRootPathProvider(new EditionSelector());
            $this->editionPathProvider = new EditionPathProvider($editionPathSelector);
        }

        return $this->editionPathProvider;
    }

    /**
     * @return string
     */
    protected function getConfigKey()
    {
        $configKey = $this->getShopConfig()->getVar('sConfigKey');
        return $configKey ?: Config::DEFAULT_CONFIG_KEY;
    }

    /**
     * Returns OXID eShop shop id.
     *
     * @return string
     */
    private function getShopId()
    {
        return '1';
    }

    /**
     * Insert new configuration value to database.
     *
     * @param string $type
     * @param string $name
     * @param string $value
     */
    private function insertConfigValue($type, $name, $value)
    {
        $dbHandler = $this->getDbHandler();
        $shopId = 1;
        $oxid = md5("${name}_1");

        $dbHandler->query("DELETE from oxconfig WHERE oxvarname = '$name';");
        $dbHandler->query("REPLACE INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES
            ('$oxid', $shopId, '', '$name', '$type', ENCODE('{$value}','{$this->getConfigKey()}'));");
        if ($this->getServiceConfig()->getShopEdition() == EditionSelector::ENTERPRISE) {
            $oxid = md5("${name}_subshop");
            $shopId = 2;
            $dbHandler->query("REPLACE INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES
                ('$oxid', $shopId, '', '$name', '$type', ENCODE('{$value}','{$this->getConfigKey()}'));");
        }
    }

    /**
     * Updates configuration value.
     *
     * @param string $id
     * @param string $value
     */
    private function updateConfigValue($id, $value)
    {
        $dbHandler = $this->getDbHandler();

        $value = is_array($value) ? serialize($value) : $value;
        $value = $dbHandler->escape($value);
        $dbHandler->query("update oxconfig set oxvarvalue = ENCODE( '{$value}','{$this->getConfigKey()}') where oxvarname = '{$id}';");
    }

    /**
     * Converts input string to utf8.
     *
     * @param string $input String for conversion.
     *
     * @return array|string
     */
    private function stringToUtf($input)
    {
        if (is_array($input)) {
            $temp = array();
            foreach ($input as $key => $value) {
                $temp[$this->stringToUtf($key)] = $this->stringToUtf($value);
            }
            $input = $temp;
        } elseif (is_string($input)) {
            $input = iconv('iso-8859-15', 'utf-8', $input);
        }

        return $input;
    }
}
