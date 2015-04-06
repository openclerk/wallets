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
 * Represents the Bitcurex PLN exchange wallet.
 * The old pln.bitcurex.com service; the new site has an unsafe accounts API, thus disabled.
 */
class BitcurexPLN extends AbstractWallet implements DisabledAccount {

  public function getName() {
    return "Bitcurex PLN";
  }

  public function getCode() {
    return "bitcurex_pln";
  }

  public function getURL() {
    return "https://pln.bitcurex.com";
  }

  public function disabledAt() {
    return "2014-09-10";
  }

  public function getFields() {
    return array(
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#^[a-f0-9\\-]{64}$#',
      ),
      'api_secret' => array(
        'title' => "API Secret",
        'regexp' => "#^[A-Za-z0-9/\\+=]{60,100}$#",
      ),
    );
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {
    // there is no public API to list supported currencies
    return array('btc', 'pln');
  }

  /**
   * @return all account balances
   * @throws AccountFetchException if something bad happened
   */
  public function fetchBalances($account, CurrencyFactory $factory, Logger $logger) {
    throw new AccountFetchException("Cannot fetch balances of disabled account");
  }

}
