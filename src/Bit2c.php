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
 * Represents the Bit2c exchange wallet.
 * Just returns hashrates, does not return balances (this is provided with CEX.io)
 */
class Bit2c extends AbstractWallet {

  public function getName() {
    return "Bit2c";
  }

  public function getCode() {
    return "bit2c";
  }

  public function getURL() {
    return "https://www.bit2c.co.il/";
  }

  public function getFields() {
    return array(
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#^[a-f0-9\\-]{36}$#',
      ),
      'api_secret' => array(
        'title' => "API Secret",
        'regexp' => "#^[A-Z0-9]{64}$#",
      ),
    );
  }

  function getBit2cCode($str) {
    switch (strtolower($str)) {
      case "ils": return "NIS";
      default:
        return strtoupper($str);
    }
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {
    // there is no public API to list supported currencies
    return array('btc', 'ltc', 'ils');
  }

  /**
   * @return all account balances
   * @throws AccountFetchException if something bad happened
   */
  public function fetchBalances($account, CurrencyFactory $factory, Logger $logger) {

    $url = "https://www.bit2c.co.il/Account/Balance/v2";
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
    foreach ($this->fetchSupportedCurrencies($factory, $logger) as $cur) {
      $result[$cur] = array(
        'confirmed' => $json[$this->getBit2cCode($cur)],
      );
    }
    return $result;

  }

  public function generatePostData($account) {
    // generate a nonce
    $req['nonce'] = time();

    // generate the POST data string
    $post_data = http_build_query($req, '', '&');

    // generate the extra headers
    $headers = array(
      'Content-Type: application/x-www-form-urlencoded',
      'Key: ' . $account['api_key'],
      'Sign: ' . base64_encode(hash_hmac('sha512', $post_data, strtoupper($account['api_secret']), true)),
    );

    return array(
      'headers' => $headers,
      'post_data' => $post_data,
    );
  }

}
