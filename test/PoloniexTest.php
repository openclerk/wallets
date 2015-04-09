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
 * Tests the {@link Poloniex} account type.
 */
class PoloniexTest extends AbstractWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\Poloniex());
  }

  /**
   * Get some field values for a valid account.
   * @return array of fields
   */
  function getValidAccount() {
    return array(
      'api_key' => 'AJ053NK6-GK018FYV-BCM33KA6-EUWSVVXV',
      'api_secret' => '07957ebd11ecf56a3c34a80e1130d23915d0a1c9bd8f99b98248612a3e4e8f4f6eb9c744358140625686c84417e18427aecb4a169bf0855304be6f73a96d1b0b',
      'accept' => true,
    );
  }

  /**
   * Get some field values for a missing account,
   * but one that is still valid according to the fields.
   * @return array of fields
   */
  function getMissingAccount() {
    return array(
      'api_key' => 'AJ053NK6-GK018FYV-BCM33KA6-EUWSVVX0',
      'api_secret' => '07957ebd11ecf56a3c34a80e1130d23915d0a1c9bd8f99b98248612a3e4e8f4f6eb9c744358140625686c84417e18427aecb4a169bf0855304be6f73a96d1b0b',
      'accept' => true,
    );
  }

  /**
   * Get some invalid field values.
   * @return array of fields
   */
  function getInvalidAccount() {
    return array(
      'api_key' => 'AJ053NK6-GK018FYV-BCM33KA6-EUWSVVXV',
      'api_secret' => '07957ebd11ecf56a3c34a80e1130d23915d0a1c9bd8f99b98248612a3e4e8f4f6eb9c744358140625686c84417e18427aecb4a169bf0855304be6f73a96d1b0b',
      'accept' => false,
    );
  }

  function doTestValidValues($balances) {
    $this->assertEquals(0, $balances['btc']['confirmed']);
    $this->assertEquals(0, $balances['ltc']['confirmed']);
    $this->assertEquals(0, $balances['dog']['confirmed']);
  }

}
