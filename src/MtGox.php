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
 * Represents the MtGox exchange wallet.
 */
class MtGox extends AbstractWallet implements DisabledAccount {

  public function disabledAt() {
    return "2014-11-07";
  }

  public function getName() {
    return "Mt.Gox";
  }

  function getCode() {
    return "mtgox";
  }

  function getURL() {
    return "https://mtgox.com/";
  }

  public function getFields() {
    return array(
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#^[a-z0-9\-]{36}$#',
      ),
      'api_secret' => array(
        'title' => "API Secret",
        'regexp' => '#^[A-Za-z0-9/\\+=]{36,}$#',
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
