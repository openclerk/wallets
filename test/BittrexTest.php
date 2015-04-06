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
 * Tests the {@link Bittrex} account type.
 */
class BittrexTest extends AbstractWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\Bittrex());
  }

  /**
   * Get some field values for a valid account.
   * @return array of fields
   */
  function getValidAccount() {
    return array(
      'api_key' => '3ccff5ea7a964e6e86d75107dd78b49f',
      'api_secret' => '76ed78dc4828422a9b58abffb82b7cb6',
    );
  }

  /**
   * Get some field values for a missing account,
   * but one that is still valid according to the fields.
   * @return array of fields
   */
  function getMissingAccount() {
    return array(
      'api_key' => '3ccff5ea7a964e6e86d75107dd78b49f',
      'api_secret' => '76ed78dc4828422a9b58abffb82b7cb1',
    );
  }

  /**
   * Get some invalid field values.
   * @return array of fields
   */
  function getInvalidAccount() {
    return array(
      'api_key' => '3ccff5ea7a964e6e86d75107dd78b49f',
      'api_secret' => 'hello',
    );
  }

  function doTestValidValues($balances) {
    $this->assertEquals(5, $balances['dog']['confirmed']);
    $this->assertFalse(isset($balances['btc']['confirmed']));
  }

}
