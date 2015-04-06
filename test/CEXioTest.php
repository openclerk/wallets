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
 * Tests the {@link CEXio} account type.
 */
class CEXioTest extends AbstractWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\CEXio());
  }

  /**
   * Get some field values for a valid account.
   * @return array of fields
   */
  function getValidAccount() {
    return array(
      'api_username' => 'openclerk',
      'api_key' => 'EP34KPz0MTuzdEQuYKmfhZNcd4',
      'api_secret' => 'VO0oNLFNu06Y6wBbCqaANR6yRd4',
    );
  }

  /**
   * Get some field values for a missing account,
   * but one that is still valid according to the fields.
   * @return array of fields
   */
  function getMissingAccount() {
    return array(
      'api_username' => 'openclerk',
      'api_key' => 'EP34KPz0MTuzdEQuYKmfhZNcd4',
      'api_secret' => 'VO0oNLFNu06Y6wBbCqaANR6yRd0',
    );
  }

  /**
   * Get some invalid field values.
   * @return array of fields
   */
  function getInvalidAccount() {
    return array(
      'api_username' => 'openclerk',
      'api_key' => 'xTcIrV0e6pw0gqHlGflECleC4Q1bvxcbxcvxcnxcngnxfgnx',
      'api_secret' => 'xTcIrV0e6pw0gqHlGflECleC',
    );
  }

  function doTestValidValues($balances) {
    // this account has no balances
    $this->assertArrayNotHasKey('btc', $balances);
    $this->assertArrayNotHasKey('nmc', $balances);
    $this->assertArrayNotHasKey('usd', $balances);
    $this->assertArrayNotHasKey('eur', $balances);
    $this->assertArrayNotHasKey('ghs', $balances);
  }

}
