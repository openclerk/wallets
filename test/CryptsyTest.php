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
 * Tests the {@link Cryptsy} account type.
 */
class CryptsyTest extends AbstractWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\Cryptsy());

    // public API key details for cryptsy API
    \Openclerk\Config::overwrite(array(
      "exchange_cryptsy_key" => "21222550a305da84dc",
      "exchange_cryptsy_secret" => "openclerk/exchanges",
    ));
  }

  /**
   * Get some field values for a valid account.
   * @return array of fields
   */
  function getValidAccount() {
    return array(
      'api_key' => '21222550a305da84dc',
      'api_secret' => 'openclerk/exchanges',
    );
  }

  /**
   * Get some field values for a missing account,
   * but one that is still valid according to the fields.
   * @return array of fields
   */
  function getMissingAccount() {
    return array(
      'api_key' => '21222550a305da84d1',
      'api_secret' => 'openclerk/exchanges',
    );
  }

  /**
   * Get some invalid field values.
   * @return array of fields
   */
  function getInvalidAccount() {
    return array(
      'api_key' => 'hello',
      'api_secret' => 'openclerk/exchanges',
    );
  }

  function doTestValidValues($balances) {
    $this->assertEquals(0, $balances['btc']['confirmed']);
    $this->assertEquals(0, $balances['ltc']['confirmed']);
    $this->assertEquals(0, $balances['dog']['confirmed']);
  }

}
