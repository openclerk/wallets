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
 * Tests the {@link CryptoTrade} account type.
 */
class CryptoTradeTest extends AbstractDisabledWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\CryptoTrade());
  }

}
