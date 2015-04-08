<?php

namespace Account\Wallet\Tests;

use Monolog\Logger;
use Account\AccountType;
use Account\AccountFetchException;
use Account\Tests\AbstractActiveAccountTest;
use Openclerk\Config;
use Openclerk\Currencies\Currency;

/**
 * Abstracts away common test functionality.
 */
abstract class AbstractWalletTest extends AbstractActiveAccountTest {

  public function __construct(AccountType $type) {
    parent::__construct($type);
    Config::merge(array(
      // reduce throttle time for tests
      "accounts_throttle" => 1,
    ));
  }

  /**
   * In openclerk/wallets, extend this to return instances of openclerk/cryptocurrencies
   */
  function loadCurrency($cur) {
    switch ($cur) {
      case "dog":
        return new \Cryptocurrency\Dogecoin();

      default:
        return null;
    }
  }

  static $tested_codes = array();

  function testUniqueCode() {
    $code = $this->account->getCode();
    $this->assertFalse(isset(self::$tested_codes[$code]), "We've already tested an account '$code'");
    self::$tested_codes[$code] = $code;
  }

  function testCodeInAccountsJson() {
    $json = json_decode(file_get_contents(__DIR__ . "/../accounts.json"), true /* assoc */);
    $code = $this->account->getCode();
    $this->assertTrue(isset($json[$code]), "Expected '$code' account in accounts.json");
    $this->assertEquals("\\" . get_class($this->account), $json[$code], "Expected '$code' to return the same class");
  }

}
