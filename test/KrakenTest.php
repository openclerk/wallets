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
 * Tests the {@link Kraken} account type.
 */
class KrakenTest extends AbstractWalletTest {

  function __construct() {
    parent::__construct(new \Account\Wallet\Kraken());
  }

  /**
   * Get some field values for a valid account.
   * @return array of fields
   */
  function getValidAccount() {
    return array(
      'api_key' => 'b2WnNMMGCtwd7qmSJ8LgkcrukPp0RBjFC/YLR+xURKK1Zf4FY9KhWNtA',
      'api_secret' => 'sz+nfnxDpaTOFfr4zcNqK2YYv0moz6XfQXdVscUrahXMMwewROwONub6XxseMjykiTur9jDx8TeOvT8JyshguA==',
    );
  }

  /**
   * Get some field values for a missing account,
   * but one that is still valid according to the fields.
   * @return array of fields
   */
  function getMissingAccount() {
    return array(
      'api_key' => 'b2WnNMMGCtwd7qmSJ8LgkcrukPp0RBjFC/YLR+xURKK1Zf4FY9KhWNt1',
      'api_secret' => 'sz+nfnxDpaTOFfr4zcNqK2YYv0moz6XfQXdVscUrahXMMwewROwONub6XxseMjykiTur9jDx8TeOvT8JyshguA==',
    );
  }

  /**
   * Get some invalid field values.
   * @return array of fields
   */
  function getInvalidAccount() {
    return array(
      'api_key' => 'b2WnNMMGCtwd7qmSJ8LgkcrukPp0RBjFC/YLR+xURKK1Zf4FY9KhWNtAzvcxzvcx',
      'api_secret' => 'sz+nfnxDpaTOFfr4zcNqK2YYv0moz6XfQXdVscUrahXMMwewROwONub6XxseMjykiTur9jDx8TeOvT8JyshguA==',
    );
  }

  function doTestValidValues($balances) {
    // this account has no balances
    $this->assertArrayNotHasKey('btc', $balances);
    $this->assertArrayNotHasKey('usd', $balances);
    $this->assertArrayNotHasKey('ltc', $balances);
    $this->assertArrayNotHasKey('dog', $balances);
  }

}
