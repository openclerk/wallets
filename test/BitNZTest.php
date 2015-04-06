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
 * Tests the {@link BitNZ} account type.
 */
class BitNZTest extends AbstractWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\BitNZ());
  }

  /**
   * Get some field values for a valid account.
   * @return array of fields
   */
  function getValidAccount() {
    return array(
      'api_username' => 'openclerk',
      'api_key' => 'bc26b78d2c2dac6cb5b2b2892f9f86d0',
      'api_secret' => 'blQ0FER7eA',
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
      'api_key' => 'bc26b78d2c2dac6cb5b2b2892f9f86d1',
      'api_secret' => 'blQ0FER7eA',
    );
  }

  /**
   * Get some invalid field values.
   * @return array of fields
   */
  function getInvalidAccount() {
    return array(
      'api_username' => 'openclerk',
      'api_key' => 'bc26b78d2c2dac6cb5b2b2892f9f86d0AB',
      'api_secret' => 'blQ0FER7eA',
    );
  }

  function doTestValidValues($balances) {
    $this->assertEquals(0, $balances['btc']['confirmed']);
    $this->assertEquals(0, $balances['nzd']['confirmed']);
  }

}
