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
 * Represents the BitNZ exchange wallet.
 */
class BitNZ extends AbstractWallet {

  public function getName() {
    return "BitNZ";
  }

  public function getCode() {
    return "bitnz";
  }

  public function getURL() {
    return "https://bitnz.com/";
  }

  public function getFields() {
    return array(
      'api_username' => array(
        'title' => "Username",
        'regexp' => '#.+#',
      ),
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#^[a-f0-9]{32}$#',
      ),
      'api_secret' => array(
        'title' => "API Secret",
        'regexp' => "#^[a-zA-Z0-9]{8,12}$#",
      ),
    );
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {
    // there is no public API to list supported currencies
    return array('btc', 'nzd');
  }

  /**
   * @return all account balances
   * @throws AccountFetchException if something bad happened
   */
  public function fetchBalances($account, CurrencyFactory $factory, Logger $logger) {

    $url = "https://bitnz.com/api/0/private/balance";
    $logger->info($url);

    try {
      $this->throttle($logger);
      $post_data = $this->generatePostData($account);
      $logger->info($post_data);
      $raw = Fetch::post($url, $post_data);
    } catch (FetchHttpException $e) {
      throw new AccountFetchException($e->getContent(), $e);
    }

    $json = Fetch::jsonDecode($raw);
    if (isset($json['message'])) {
      throw new AccountFetchException($json['message']);
    }

    $result = array();
    foreach ($this->fetchSupportedCurrencies($factory, $logger) as $cur) {
      $result[$cur] = array(
        'confirmed' => $json[$cur . "_balance"],
        'reserved' => $json[$cur . "_reserved"],
        'available' => $json[$cur . "_available"],
      );
    }
    return $result;

  }

  public function generatePostData($account) {
    $nonce = time();
    $message = $nonce . $account['api_username'] . $account['api_key'];
    $signature = strtoupper(hash_hmac("sha256", $message, $account['api_secret']));

    // generate the POST data string
    $req = array(
      'key' => $account['api_key'],
      'signature' => $signature,
      'nonce' => $nonce,
    );
    $post_data = http_build_query($req, '', '&');
    return $post_data;
  }

}
