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
 * Tests the {@link Bitstamp} account type.
 */
class BitstampTest extends AbstractWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\Bitstamp());
  }

  /**
   * Get some field values for a valid account.
   * @return array of fields
   */
  function getValidAccount() {
    return array(
      'api_client_id' => '4798',
      'api_key' => 'Bs3v4SkwE4HO4FWFh8QLmbuWtrSRrVnw',
      'api_secret' => 'G1oiyofmWJAcnY3lQIhUiHJBGrKHhcaR',
    );
  }

  /**
   * Get some field values for a missing account,
   * but one that is still valid according to the fields.
   * @return array of fields
   */
  function getMissingAccount() {
    return array(
      'api_client_id' => '4799',
      'api_key' => 'Bs3v4SkwE4HO4FWFh8QLmbuWtrSRrVn1',
      'api_secret' => 'G1oiyofmWJAcnY3lQIhUiHJBGrKHhcaR',
    );
  }

  /**
   * Get some invalid field values.
   * @return array of fields
   */
  function getInvalidAccount() {
    return array(
      'api_client_id' => 'hello',
      'api_key' => 'Bs3v4SkwE4HO4FWFh8QLmbuWtrSRrVn1',
      'api_secret' => 'G1oiyofmWJAcnY3lQIhUiHJBGrKHhcaR',
    );
  }

  function doTestValidValues($balances) {
    $this->assertEquals(0, $balances['btc']['confirmed']);
    $this->assertEquals(0, $balances['usd']['confirmed']);
  }

}
