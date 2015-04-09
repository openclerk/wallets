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
 * Represents the new Justcoin exchange wallet, hosted through ANX.
 * This is necessary because there are new API key and secret formats (issue #468).
 */
class JustcoinAnx extends AbstractAnx {

  public function getName() {
    return "Justcoin";
  }

  public function getCode() {
    return "justcoin_anx";
  }

  function getURL() {
    return "https://justcoin.com/";
  }

  function getBalanceURL() {
    return "https://justcoin.com/api/2/money/info";
  }

}
