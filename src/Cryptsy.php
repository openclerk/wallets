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
use \Openclerk\Config;
use \Openclerk\Currencies\CurrencyFactory;

/**
 * Represents the Cryptsy exchange wallet.
 */
class Cryptsy extends AbstractWallet {

  public function getName() {
    return "Cryptsy";
  }

  public function getCode() {
    return "cryptsy";
  }

  public function getURL() {
    return "https://www.cryptsy.com/";
  }

  public function getFields() {
    return array(
      'api_key' => array(
        'title' => "API Key",
        // looks like a 40 character hex string (full trade) or 18-19 characters (application keys)
        'regexp' => '#^[a-f0-9]{16,40}#',
      ),
      'api_secret' => array(
        'title' => "API Secret",
        // can be anything
        'regexp' => "#.+$#",
      ),
    );
  }

  /**
   * Convert the given Cryptsy currency code (uppercase)
   * into the openclerk/currencies currency code (lowercase, three characters)
   * so that it can be used in openclerk/currencies
   */
  function getCurrencyCode($str) {
    switch (strtoupper($str)) {
      // exceptions
      case "DOGE": return "dog";
      case "BLK": return "bc1";

      // otherwise return lowercase
      default:
        return strtolower($str);
    }
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {
    // we can emulate this with a normal balance call
    $key = Config::get('exchange_cryptsy_key');   // aka Application Key
    $secret = Config::get('exchange_cryptsy_secret');   // aka Application/Device ID

    $balances = $this->fetchBalances(array(
      'api_key' => $key,
      'api_secret' => $secret,
    ), $factory, $logger);

    return array_keys($balances);
  }

  /**
   * @return all account balances
   * @throws AccountFetchException if something bad happened
   */
  public function fetchBalances($account, CurrencyFactory $factory, Logger $logger) {

    $url = "https://www.cryptsy.com/api";
    $logger->info($url);

    try {
      $this->throttle($logger);
      $post_data = $this->generatePostData($account);
      $raw = Fetch::post($url, $post_data['post_data'], array(), $post_data['headers']);
    } catch (FetchHttpException $e) {
      throw new AccountFetchException($e->getMessage(), $e);
    }

    $json = Fetch::jsonDecode($raw);
    if (!$json['success']) {
      throw new AccountFetchException($json['error']);
    }

    $result = array();
    foreach ($json['return']['balances_available'] as $cur => $balance) {
      $currency = $this->getCurrencyCode($cur);
      if (strlen($currency) === 3) {
        $result[$currency] = array(
          'confirmed' => $balance,
        );
      }
    }

    return $result;

  }

  public function generatePostData($account) {
    $req = array();
    $req['method'] = "getinfo";
    $mt = explode(' ', microtime());
    $req['nonce'] = $mt[1];

    // generate the POST data string
    $post_data = http_build_query($req, '', '&');

    // generate the extra headers
    $headers = array(
      'Sign: ' . hash_hmac('sha512', $post_data, $account['api_secret']),
      'Key: ' . $account['api_key'],
    );

    return array(
      'post_data' => $post_data,
      'headers' => $headers,
    );
  }

}
