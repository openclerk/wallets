<?php

namespace Account\Wallet\Tests;

use Monolog\Logger;
use Account\AccountType;
use Account\AccountFetchException;
use Account\Tests\AbstractAccountTest;
use Openclerk\Config;
use Openclerk\Currencies\Currency;

/**
 * Abstracts away common test functionality.
 */
abstract class AbstractDisabledWalletTest extends AbstractAccountTest {

  function getAccountsJSON() {
    return __DIR__ . "/../accounts.json";
  }

}
