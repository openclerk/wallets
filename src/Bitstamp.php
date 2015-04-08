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
 * Represents the Bitstamp exchange wallet.
 */
class Bitstamp extends AbstractWallet {

  public function getName() {
    return "Bitstamp";
  }

  function getCode() {
    return "bitstamp";
  }

  function getURL() {
    return "https://www.bitstamp.net/";
  }

  public function getFields() {
    return array(
      'api_client_id' => array(
        'title' => "Customer ID",
        'regexp' => '#^[0-9]+$#',
      ),
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#^[A-Za-z0-9]{32}$#',
      ),
      'api_secret' => array(
        'title' => "API Secret",
        'regexp' => '#^[A-Za-z0-9]{32}$#',
      ),
    );
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {
    // there is no public API to list supported currencies
    return array('btc', 'usd');
  }

  /**
   * @return all account balances
   * @throws AccountFetchException if something bad happened
   */
  public function fetchBalances($account, CurrencyFactory $factory, Logger $logger) {

    $url = "https://www.bitstamp.net/api/balance/";
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
    if (isset($json['error'])) {
      throw new AccountFetchException($json['error']);
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
    $message = $nonce . sprintf("%06d", (int) $account['api_client_id']) . $account['api_key'];   // the $client_id must be six digits or longer; not specified in API, to prevent "Invalid signature" errors
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
