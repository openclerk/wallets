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
 * Tests the {@link Anxpro} account type.
 */
class AnxproTest extends AbstractWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\Anxpro());
  }

  /**
   * Get some field values for a valid account.
   * @return array of fields
   */
  function getValidAccount() {
    return array(
      'api_key' => 'bfefcbc0-003f-4d92-ac5b-400243b6ba93',
      'api_secret' => 'yFZEF4jXUFemSQMl1g6EzWfuoNwKKnl20139noVlOhxI/rTIDDb0JcykVfoehynD+JWNAJigYOteDaGGIIKStA==',
    );
  }

  /**
   * Get some field values for a missing account,
   * but one that is still valid according to the fields.
   * @return array of fields
   */
  function getMissingAccount() {
    return array(
      'api_key' => 'bfefcbc0-003f-4d92-ac5b-400243b6ba93',
      'api_secret' => 'yFZEF4jXUFemSQMl1g6EzWfuoNwKKnl20139noVlOhxI/rTIDDb0JcykVfoehynD+JWNAJigYOteDaGGIIKSt1==',
    );
  }

  /**
   * Get some invalid field values.
   * @return array of fields
   */
  function getInvalidAccount() {
    return array(
      'api_key' => 'hello',
      'api_secret' => 'yFZEF4jXUFemSQMl1g6EzWfuoNwKKnl20139noVlOhxI/rTIDDb0JcykVfoehynD+JWNAJigYOteDaGGIIKSt1==',
    );
  }

  function doTestValidValues($balances) {
    $this->assertEquals(0, $balances['btc']['confirmed']);
    $this->assertEquals(0, $balances['nzd']['confirmed']);
    $this->assertEquals(0, $balances['dog']['confirmed']);
    $this->assertEquals(0, $balances['ltc']['confirmed']);
  }

}
