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
 * Represents the Justcoin exchange wallet.
 */
class Justcoin extends AbstractWallet implements DisabledAccount {

  public function disabledAt() {
    return "2015-04-09";
  }

  public function getName() {
    return "Justcoin";
  }

  function getCode() {
    return "justcoin";
  }

  function getURL() {
    return "https://justcoin.com/";
  }

  public function getFields() {
    return array(
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#^[a-f0-9]{64}$#',
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
