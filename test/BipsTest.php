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
 * Tests the {@link Bips} account type.
 */
class BipsTest extends AbstractAccountTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\Bips());
  }

}
