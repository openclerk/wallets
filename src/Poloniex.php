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
 * Represents the Poloniex exchange wallet.
 */
class Poloniex extends AbstractWallet {

  public function getName() {
    return "Poloniex";
  }

  public function getCode() {
    return "poloniex";
  }

  public function getURL() {
    return "https://poloniex.com/";
  }

  public function getFields() {
    return array(
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#^[A-Z0-9]{8}-[A-Z0-9]{8}-[A-Z0-9]{8}-[A-Z0-9]{8}+$#',
      ),
      'api_secret' => array(
        'title' => "API Secret",
        'regexp' => "#^[a-f0-9]{128}$#",
      ),
      'accept' => array(
        'title' => "I accept that this API is unsafe",
        'type' => "confirm",
        'note' => array("A :title API key allows trading, but does not allow withdrawl." /* i18n */, array(":title" => $this->getName())),
      ),
    );
  }

  /**
   * Convert the given Poloniex currency code (uppercase)
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
    $url = "https://poloniex.com/public?command=returnCurrencies";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    $result = array();

    foreach ($json as $key => $market) {
      // allow disabled markets to be included;
      // the openclerk/exchanges implementation can deal with pairs that aren't traded any more
      // if ($market['disabled']) ...

      $currency = $this->getCurrencyCode($key);
      if (strlen($currency) === 3) {
        $result[] = $currency;
      }
    }

    return array_unique($result);
  }

  /**
   * @return all account balances
   * @throws AccountFetchException if something bad happened
   */
  public function fetchBalances($account, CurrencyFactory $factory, Logger $logger) {

    if (!$account['accept']) {
      throw new AccountFetchException("Cannot fetch balances without accepting unsafety of API");
    }

    $url = "https://poloniex.com/tradingApi";
    $logger->info($url);

    try {
      $this->throttle($logger);
      $post_data = $this->generatePostData($account);
      $raw = Fetch::post($url, $post_data['post_data'], array(), $post_data['headers']);
    } catch (FetchHttpException $e) {
      throw new AccountFetchException($e->getMessage(), $e);
    }

    $json = Fetch::jsonDecode($raw);
    if (isset($json['error']) && $json['error']) {
      throw new AccountFetchException($json['error']);
    }

    $result = array();
    foreach ($json as $cur => $balance) {
      $currency = $this->getCurrencyCode($cur);
      if (strlen($currency) === 3) {
        $result[$currency] = array(
          'confirmed' => $balance['available'] + $balance['onOrders'],
          'available' => $balance['available'],
          'reserved' => $balance['onOrders'],
        );
      }
    }

    return $result;

  }

  public function generatePostData($account) {
    $req = array();
    $req['command'] = "returnCompleteBalances";

    // generate a nonce as microtime, with as-string handling to avoid problems with 32bits systems
    $mt = explode(' ', microtime());
    $req['nonce'] = $mt[1] . substr($mt[0], 2, 6);

    // generate the POST data string
    $post_data = http_build_query($req, '', '&');

    // generate the extra headers
    $headers = array(
      'Key: ' . $account['api_key'],
      'Sign: ' . hash_hmac('sha512', $post_data, $account['api_secret']),
    );

    return array(
      'post_data' => $post_data,
      'headers' => $headers,
    );
  }

}
