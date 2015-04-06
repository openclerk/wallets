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
 * Represents the BitMarket.pl exchange wallet.
 */
class BitMarketPl extends AbstractWallet {

  public function getName() {
    return "BitMarket.pl";
  }

  public function getCode() {
    return "bitmarket_pl";
  }

  public function getURL() {
    return "https://www.bitmarket.pl/";
  }

  public function getFields() {
    return array(
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#^[a-f0-9]{32}$#',
      ),
      'api_secret' => array(
        'title' => "API Secret",
        'regexp' => '#^[a-f0-9]{32}$#',
      ),
    );
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {
    // there is no public API to list supported currencies
    return array('btc', 'ltc', 'dog', 'ppc', 'pln', 'eur');
  }

  function getBitMarketPlCode($str) {
    switch (strtolower($str)) {
      case "dog": return "DOGE";
      default:
        return strtoupper($str);
    }
  }

  /**
   * @return all account balances
   * @throws AccountFetchException if something bad happened
   */
  public function fetchBalances($account, CurrencyFactory $factory, Logger $logger) {

    $url = "https://www.bitmarket.pl/api2/";
    $logger->info($url);

    try {
      $this->throttle($logger);
      $post_data = $this->generatePostData($account);
      $logger->info($post_data['post_data']);
      $raw = Fetch::post($url, $post_data['post_data'], array(), $post_data['headers']);
    } catch (FetchHttpException $e) {
      throw new AccountFetchException($e->getMessage(), $e);
    }

    $json = Fetch::jsonDecode($raw);
    if (isset($json['errorMsg'])) {
      throw new AccountFetchException($json['errorMsg']);
    }
    if (isset($json['error'])) {
      throw new AccountFetchException($json['error']);
    }

    $result = array();
    foreach ($this->fetchSupportedCurrencies($factory, $logger) as $cur) {
      $result[$cur] = array(
        'available' => $json['data']['balances']['available'][$this->getBitMarketPlCode($cur)],
      );

      if (isset($json['data']['balances']['blocked'][$this->getBitMarketPlCode($cur)])) {
        $result[$cur]['blocked'] = $json['data']['balances']['blocked'][$this->getBitMarketPlCode($cur)];
        $result[$cur]['confirmed'] = $result[$cur]['available'] + $result[$cur]['blocked'];
      } else {
        $result[$cur]['confirmed'] = $result[$cur]['available'];
      }
    }

    return $result;

  }

  public function generatePostData($account) {
    $req = array();
    $req['method'] = "info";
    $req['tonce'] = time();

    // generate the POST data string
    $post = http_build_query($req, '', '&');
    $sign = hash_hmac("sha512", $post, $account['api_secret']);

    // generate the extra headers
    $headers = array(
      'API-Key: ' . $account['api_key'],
      'API-Hash: ' . $sign,
    );

    return array(
      'headers' => $headers,
      'post_data' => $post,
    );
  }

}
