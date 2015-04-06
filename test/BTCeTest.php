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
 * Tests the {@link BTCe} account type.
 */
class BTCeTest extends AbstractWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\BTCe());
  }

  /**
   * Get some field values for a valid account.
   * @return array of fields
   */
  function getValidAccount() {
    return array(
      'api_key' => 'HCXLP6TV-L8ZQ1G27-QHYEUZ0T-7BUTE629-2YAN6FE9',
      'api_secret' => 'e2683dfe2dd123c862de71679e4a8a90675a5e9083323f4d8061e600f8797f18',
    );
  }

  /**
   * Get some field values for a missing account,
   * but one that is still valid according to the fields.
   * @return array of fields
   */
  function getMissingAccount() {
    return array(
      'api_key' => 'HCXLP6TV-L8ZQ1G27-QHYEUZ0T-7BUTE629-2YAN6FE9',
      'api_secret' => 'e2683dfe2dd123c862de71679e4a8a90675a5e9083323f4d8061e600f8797f10',
    );
  }

  /**
   * Get some invalid field values.
   * @return array of fields
   */
  function getInvalidAccount() {
    return array(
      'api_key' => 'hello',
      'api_secret' => 'e2683dfe2dd123c862de71679e4a8a90675a5e9083323f4d8061e600f8797f10',
    );
  }

  function doTestValidValues($balances) {
    $this->assertEquals(0, $balances['btc']['confirmed']);
    $this->assertEquals(0, $balances['ltc']['confirmed']);
  }

}
