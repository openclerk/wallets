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
 * Represents the BTC-e exchange wallet.
 */
class BTCe extends AbstractWallet {

  public function getName() {
    return "BTC-e";
  }

  function getCode() {
    return "btce";
  }

  function getURL() {
    return "https://btc-e.com/";
  }

  public function getFields() {
    return array(
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#^[A-Z0-9\-]{44}$#',
      ),
      'api_secret' => array(
        'title' => "API Secret",
        'regexp' => '#^[a-z0-9]{64}$#',
      ),
    );
  }

  /**
   * Convert the given BTC-e currency code (lowercase)
   * into the openclerk/currencies currency code (lowercase, three characters)
   * so that it can be used in openclerk/currencies
   */
  function getCurrencyCode($str) {
    switch (strtolower($str)) {
      // exceptions
      case "rur": return "rub";
      case "cnh": return "cny";

      // otherwise return lowercase
      default:
        return strtolower($str);
    }
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {
    $url = "https://btc-e.com/api/3/info";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    $result = array();
    foreach ($json['pairs'] as $market => $info) {
      $pairs = explode("_", $market, 2);

      $cur = $this->getCurrencyCode($pairs[0]);
      if (strlen($cur) == 3) {
        $result[] = $cur;
      }
      $cur = $this->getCurrencyCode($pairs[1]);
      if (strlen($cur) == 3) {
        $result[] = $cur;
      }
    }

    return array_unique($result);
  }

  /**
   * @return all account balances
   * @throws AccountFetchException if something bad happened
   */
  public function fetchBalances($account, CurrencyFactory $factory, Logger $logger) {

    $url = "https://btc-e.com/tapi/";
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
    if (isset($json['error'])) {
      throw new AccountFetchException($json['error']);
    }

    $result = array();
    foreach ($json['return']['funds'] as $cur => $balance) {
      $cur = $this->getCurrencyCode($cur);

      // special exceptions for currencies with wallets but can't be traded
      if ($cur == "trc" || $cur == "ftc" || $cur == "xpm") {
        continue;
      }

      $result[$cur] = array(
        'confirmed' => $balance,
      );
    }
    return $result;

  }

  public function generatePostData($account) {
    $req = array();
    $req['method'] = 'getInfo';
    $mt = explode(' ', microtime());
    $req['nonce'] = $mt[1];

    // generate the POST data string
    $post_data = http_build_query($req, '', '&');

    $sign = hash_hmac("sha512", $post_data, $account['api_secret']);

    // generate the extra headers
    $headers = array(
      'Sign: ' . $sign,
      'Key: ' . $account['api_key'],
    );

    return array(
      'post_data' => $post_data,
      'headers' => $headers,
    );
  }

}
