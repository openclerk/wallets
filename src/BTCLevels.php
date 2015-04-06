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
 * Represents the BTCLevels exchange wallet.
 */
class BTCLevels extends AbstractWallet {

  public function getName() {
    return "BTCLevels";
  }

  public function getCode() {
    return "btclevels";
  }

  public function getURL() {
    return "https://btclevels.com/";
  }

  public function getFields() {
    return array(
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#.+#',
      ),
      'api_secret' => array(
        'title' => "API Secret",
        'regexp' => "#^[a-z0-9]{16}-[a-z0-9]{16}-[a-z0-9]{16}-[a-z0-9]{16}-[a-z0-9]{16}$#",
      ),
    );
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {
    // there is no public API to list supported currencies
    return array('btc');
  }

  /**
   * @return all account balances
   * @throws AccountFetchException if something bad happened
   */
  public function fetchBalances($account, CurrencyFactory $factory, Logger $logger) {

    $url = "https://btclevels.com/api/info";
    $logger->info($url);

    try {
      $this->throttle($logger);
      $post_data = $this->generatePostData($account);
      $raw = Fetch::post($url, $post_data);
    } catch (FetchHttpException $e) {
      throw new AccountFetchException($e->getMessage(), $e);
    }

    try {
      $json = Fetch::jsonDecode($raw);
    } catch (FetchException $e) {
      $message = strlen($raw) < 64 ? $e->getMessage() : $raw;
      throw new AccountFetchException($message, $e);
    }
    if (isset($json['error'])) {
      throw new AccountFetchException($json['error']);
    }

    $result = array(
      'btc' => array(
        'unconfirmed' => $json['info']['balance'],
        'confirmed' => $json['info']['actualbalance'],
      ),
    );

    return $result;

  }

  public function generatePostData($account) {
    $req = array(
      'key' => $account['api_key'],
      'secret' => $account['api_secret'],
    );

    return $req;
  }

}
