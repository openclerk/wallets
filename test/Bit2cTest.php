<?php

namespace Account\Wallet\Tests;

use Monolog\Logger;
use Account\AccountType;
use Account\AccountFetchException;
use Account\Tests\AbstractAccountTest;
use Account\Tests\AbstractActiveAccountTest;
use Openclerk\Config;
use Openclerk\Currencies\Currency;

/**
 * Tests the {@link Bit2c} account type.
 */
class Bit2cTest extends AbstractWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\Bit2c());
  }

  /**
   * Get some field values for a valid account.
   * @return array of fields
   */
  function getValidAccount() {
    return array(
      'api_key' => '053644da-fc0d-4cd0-a888-6b38d69863e5',
      'api_secret' => '9A83F173C1655D3C3E6F5EBDE8A50BB83F045C3332ADF1117F58DADCFF83CB6D',
    );
  }

  /**
   * Get some field values for a missing account,
   * but one that is still valid according to the fields.
   * @return array of fields
   */
  function getMissingAccount() {
    return array(
      'api_key' => '053644da-fc0d-4cd0-a888-6b38d69863e5',
      'api_secret' => '9A83F173C1655D3C3E6F5EBDE8A50BB83F045C3332ADF1117F58DADCFF83CB61',
    );
  }

  /**
   * Get some invalid field values.
   * @return array of fields
   */
  function getInvalidAccount() {
    return array(
      'api_key' => 'hello',
      'api_secret' => '9A83F173C1655D3C3E6F5EBDE8A50BB83F045C3332ADF1117F58DADCFF83CB6D',
    );
  }

  function doTestValidValues($balances) {
    $this->assertEquals(0, $balances['btc']['confirmed']);
    $this->assertEquals(0, $balances['ltc']['confirmed']);
    $this->assertEquals(0, $balances['ils']['confirmed']);
  }

}
