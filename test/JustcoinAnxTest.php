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
 * Tests the {@link JustcoinAnx} account type.
 */
class JustcoinAnxTest extends AbstractWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\JustcoinAnx());

    // public API key details for cryptsy API
    \Openclerk\Config::overwrite(array(
      "exchange_justcoin_key" => "3899bb67-b2fb-4827-a097-e394c1c728c1",
    ));
  }

  /**
   * Get some field values for a valid account.
   * @return array of fields
   */
  function getValidAccount() {
    return array(
      'api_key' => '3899bb67-b2fb-4827-a097-e394c1c728c1',
      'api_secret' => 'gB/+xfA7HateAZvDEL2IELeBFGqTD0s9OOZfq9PsJ7ZHUYrW9SV9SxKHb4YkWvFl9DEZhU03DIE9IW9jm1WQWA==',
    );
  }

  /**
   * Get some field values for a missing account,
   * but one that is still valid according to the fields.
   * @return array of fields
   */
  function getMissingAccount() {
    return array(
      'api_key' => '3899bb67-b2fb-4827-a097-e394c1c728c2',
      'api_secret' => 'gB/+xfA7HateAZvDEL2IELeBFGqTD0s9OOZfq9PsJ7ZHUYrW9SV9SxKHb4YkWvFl9DEZhU03DIE9IW9jm1WQWA==',
    );
  }

  /**
   * Get some invalid field values.
   * @return array of fields
   */
  function getInvalidAccount() {
    return array(
      'api_key' => '3899bb67-b2fb-4827-a097-e394c1c728czzz',
      'api_secret' => 'gB/+xfA7HateAZvDEL2IELeBFGqTD0s9OOZfq9PsJ7ZHUYrW9SV9SxKHb4YkWvFl9DEZhU03DIE9IW9jm1WQWA==',
    );
  }

  function doTestValidValues($balances) {
    $this->assertEquals(0, $balances['btc']['confirmed']);
    $this->assertEquals(0, $balances['ltc']['confirmed']);
    $this->assertEquals(0, $balances['dog']['confirmed']);
  }

}
