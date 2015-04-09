<?php

namespace Account\Wallet;

use \Monolog\Logger;
use \Account\Account;
use \Account\DisabledAccount;
use \Account\SimpleAccountType;
use \Account\AccountFetchException;
use \Apis\FetchHttpException;
use \Apis\FetchException;
use \Apis\Fetch;
use \Openclerk\Currencies\CurrencyFactory;

/**
 * Represents the Crypto-Trade exchange wallet.
 */
class CryptoTrade extends AbstractWallet implements DisabledAccount {

  public function disabledAt() {
    return "2015-04-09";
  }

  public function getName() {
    return "Crypto-Trade";
  }

  function getCode() {
    return "crypto-trade";
  }

  function getURL() {
    return "https://www.crypto-trade.com";
  }

  public function getFields() {
    return array(
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#^[A-F0-9]{8}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{12}$#',
      ),
      'api_secret' => array(
        'title' => "API Secret",
        'regexp' => '#^[a-f0-9]{40}$#',
      ),
    );
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {
    throw new AccountFetchException("Cannot fetch supported currencies of a disabled account");
  }

  /**
   * @return all account balances
   * @throws AccountFetchException if something bad happened
   */
  public function fetchBalances($account, CurrencyFactory $factory, Logger $logger) {
    throw new AccountFetchException("Cannot fetch balances of a disabled account");
  }

}
