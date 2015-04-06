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
 * Tests the {@link BTCLevels} account type.
 */
class BTCLevelsTest extends AbstractWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\BTCLevels());
  }

  /**
   * Get some field values for a valid account.
   * @return array of fields
   */
  function getValidAccount() {
    return array(
      'api_key' => 'openclerk',
      'api_secret' => '0gk5j8bt5pbkt1b9-azsgt707c1pewga6-wu0f3k070lq4l2bv-25cvam2mnr2j8bp4-7yj9jtfte6xz89v9',
    );
  }

  /**
   * Get some field values for a missing account,
   * but one that is still valid according to the fields.
   * @return array of fields
   */
  function getMissingAccount() {
    return array(
      'api_key' => 'openclerk',
      'api_secret' => '0gk5j8bt5pbkt1b9-azsgt707c1pewga6-wu0f3k070lq4l2bv-25cvam2mnr2j8bp4-7yj9jtfte6xz89v0',
    );
  }

  /**
   * Get some invalid field values.
   * @return array of fields
   */
  function getInvalidAccount() {
    return array(
      'api_key' => 'openclerk',
      'api_secret' => 'hello',
    );
  }

  function doTestValidValues($balances) {
    $this->assertEquals(0, $balances['btc']['confirmed']);
  }

}
