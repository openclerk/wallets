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
 * Represents the Kraken exchange wallet.
 */
class Kraken extends AbstractWallet {

  public function getName() {
    return "Kraken";
  }

  public function getCode() {
    return "kraken";
  }

  public function getURL() {
    return "https://www.kraken.com/";
  }

  public function getFields() {
    return array(
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#^[a-zA-Z0-9=/+]{56}$#',
      ),
      'api_secret' => array(
        'title' => "Private Key",
        'regexp' => "#^[a-zA-Z0-9=/+]{64,128}$#",
      ),
    );
  }

  static $currency_code_map = array(
    "XXBT" => "btc",
    "XLTC" => "ltc",
    "XXRP" => "xrp",
    "XNMC" => "nmc",
    "XXDG" => "dgc",
    "XSTR" => "str",
    "XXVN" => "ven",

    "ZUSD" => "usd",
    "ZEUR" => "eur",
    "ZGBP" => "gbp",
    "ZKRW" => "krw",
    "ZCAD" => "cad",
    "ZCNY" => "cny",
    "ZRUB" => "rur",
    "ZJPY" => "jpy",
    "ZAUD" => "aud",
  );

  /**
   * Convert the given Kraken currency code (uppercase)
   * into the openclerk/currencies currency code (lowercase, three characters)
   */
  function getCurrencyCode($str) {
    if (isset(self::$currency_code_map[$str])) {
      return self::$currency_code_map[$str];
    }

    return strtolower($str);
  }

  function getKrakenCode($str) {
    $index = array_search($str, self::$currency_code_map);
    if ($index !== false) {
      return $index;
    }

    return strtoupper($str);
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {

    $url = "https://api.kraken.com/0/public/AssetPairs";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    $result = array();
    foreach ($json['result'] as $pair => $info) {
      $currency1 = $this->getCurrencyCode(substr($pair, 0, 4));
      $currency2 = $this->getCurrencyCode(substr($pair, 4, 4));
      $result[] = $currency1;
      $result[] = $currency2;
    }

    return array_unique($result);

  }

  /**
   * @return all account balances
   * @throws AccountFetchException if something bad happened
   */
  public function fetchBalances($account, CurrencyFactory $factory, Logger $logger) {

    $url = "https://api.kraken.com/0/private/Balance";
    $logger->info($url);

    try {
      $this->throttle($logger);
      $post_data = $this->generatePostData($account, "/0/private/Balance");
      $raw = Fetch::post($url, $post_data['post_data'], array(), $post_data['headers']);
    } catch (FetchHttpException $e) {
      throw new AccountFetchException($e->getMessage(), $e);
    }

    $json = Fetch::jsonDecode($raw);
    if (isset($json['error']) && $json['error']) {
      throw new AccountFetchException(implode(", ", $json['error']));
    }

    $result = array();
    foreach ($json['result'] as $cur => $balance) {
      $currency = $this->getCurrencyCode($cur);
      $result[$currency] = array(
        'confirmed' => $balance,
      );
    }
    return $result;

  }

  public function generatePostData($account, $path) {
    $request = array();

    // generate a 64 bit nonce using a timestamp at microsecond resolution
    // string functions are used to avoid problems on 32 bit systems
    $nonce = explode(' ', microtime());
    $request['nonce'] = $nonce[1] . str_pad(substr($nonce[0], 2, 6), 6, '0');

    // generate the POST data string
    $post_data = http_build_query($request, '', '&');

    // set API key and sign the message
      $sign = hash_hmac('sha512', $path . hash('sha256', $request['nonce'] . $post_data, true), base64_decode($account['api_secret']), true);

    // generate the extra headers
    $headers = array(
      'Api-Key: ' . $account['api_key'],
      'Api-Sign: ' . base64_encode($sign),
    );

    return array(
      'post_data' => $post_data,
      'headers' => $headers,
    );
  }

}
