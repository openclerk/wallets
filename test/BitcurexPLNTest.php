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
 * Tests the {@link BitcurexPLN} account type.
 */
class BitcurexPLNTest extends AbstractDisabledWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\BitcurexPLN());
  }

}
