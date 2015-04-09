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
 * Tests the {@link Vircurex} account type.
 */
class VircurexTest extends AbstractWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\Vircurex());
  }

  /**
   * Get some field values for a valid account.
   * @return array of fields
   */
  function getValidAccount() {
    return array(
      'api_username' => 'openclerk',
      'api_secret' => 'test',
    );
  }

  /**
   * Get some field values for a missing account,
   * but one that is still valid according to the fields.
   * @return array of fields
   */
  function getMissingAccount() {
    return array(
      'api_username' => 'nobody',
      'api_secret' => 'test',
    );
  }

  /**
   * Get some invalid field values.
   * @return array of fields
   */
  function getInvalidAccount() {
    return array(
      'api_username' => '1',
      'api_secret' => '',
    );
  }

  function doTestValidValues($balances) {
    $this->assertEquals(0, $balances['btc']['confirmed']);
    $this->assertEquals(0, $balances['ltc']['confirmed']);
    $this->assertEquals(0, $balances['nmc']['confirmed']);
    $this->assertEquals(0, $balances['dog']['confirmed']);
  }

}
