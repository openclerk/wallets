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
 * Tests the {@link VaultOfSatoshi} account type.
 */
class VaultOfSatoshiTest extends AbstractDisabledWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\VaultOfSatoshi());
  }

}
