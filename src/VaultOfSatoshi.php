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
 * Represents the VaultOfSatoshi exchange wallet.
 */
class VaultOfSatoshi extends AbstractWallet implements DisabledAccount {

  function disabledAt() {
    return "2015-04-09";
  }

  function getName() {
    return "Vault of Satoshi";
  }

  function getCode() {
    return "vaultofsatoshi";
  }

  function getURL() {
    return "https://www.vaultofsatoshi.com/";
  }

  public function getFields() {
    return array(
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#^[a-f0-9]{64}$#',
      ),
      'api_secret' => array(
        'title' => "API Secret",
        'regexp' => '#^[a-f0-9]{64}$#',
      ),
    );
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {
    return array('cad', 'usd', 'btc', 'ltc', 'ppc', 'dog', 'ftc', 'xpm', 'vtc', 'bc1', 'drk');
  }

  /**
   * @return all account balances
   * @throws AccountFetchException if something bad happened
   */
  public function fetchBalances($account, CurrencyFactory $factory, Logger $logger) {
    throw new AccountFetchException("Cannot fetch balances of a disabled account");
  }

}
