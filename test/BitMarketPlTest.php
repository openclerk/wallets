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
 * Tests the {@link BitMarketPl} account type.
 */
class BitMarketPlTest extends AbstractWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\BitMarketPl());
  }

  /**
   * Get some field values for a valid account.
   * @return array of fields
   */
  function getValidAccount() {
    return array(
      'api_key' => '800448c249b3cea5cdb107719f10d22d',
      'api_secret' => '099302ad8945cacc878ce17c4f9c1afe',
    );
  }

  /**
   * Get some field values for a missing account,
   * but one that is still valid according to the fields.
   * @return array of fields
   */
  function getMissingAccount() {
    return array(
      'api_key' => '800448c249b3cea5cdb107719f10d22d',
      'api_secret' => '099302ad8945cacc878ce17c4f9c1af1',
    );
  }

  /**
   * Get some invalid field values.
   * @return array of fields
   */
  function getInvalidAccount() {
    return array(
      'api_key' => 'hello',
      'api_secret' => '099302ad8945cacc878ce17c4f9c1afe',
    );
  }

  function doTestValidValues($balances) {
    $this->assertEquals(0, $balances['btc']['confirmed']);
    $this->assertEquals(0, $balances['ltc']['confirmed']);
    $this->assertEquals(0, $balances['pln']['confirmed']);
  }

}
