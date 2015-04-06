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
 * Represents the ANXPRO exchange wallet.
 * Just returns hashrates, does not return balances (this is provided with CEX.io)
 */
class ANXPRO extends AbstractWallet {

  public function getName() {
    return "ANXPRO";
  }

  public function getCode() {
    return "anxpro";
  }

  function getURL() {
    return "https://anxpro.com/";
  }

  public function getFields() {
    return array(
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#^[a-f0-9\\-]{36}$#',
      ),
      'api_secret' => array(
        'title' => "API Secret",
        'regexp' => "#^[A-Za-z0-9/\\+=]{36,}$#",
      ),
    );
  }

  /**
   * Convert the given ANXPRO currency code (uppercase)
   * into the openclerk/currencies currency code (lowercase, three characters)
   */
  function getCurrencyCode($str) {
    switch (strtoupper($str)) {
      // exceptions
      case "DOGE": return "dog";

      // otherwise, ANXPRO uses expected currency codes
      default:
        return strtolower($str);
    }
  }

  function getANXPROCode($str) {
    switch (strtolower($str)) {
      case "dog": return "DOGE";
      default:
        return strtoupper($str);
    }
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {
    // there is no public API to list supported currencies
    return array('btc', 'usd', 'hkd', 'eur', 'cad', 'aud', 'sgd', 'jpy', 'chf', 'gbp', 'nzd', 'ltc', 'dog', 'str', 'xrp');
  }

  /**
   * @return all account balances
   * @throws AccountFetchException if something bad happened
   */
  public function fetchBalances($account, CurrencyFactory $factory, Logger $logger) {

    $url = "https://anxpro.com/api/2/money/info";
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
        'confirmed' => $json['data']['Wallets'][$this->getANXPROCode($cur)]['Balance']['value'],
        'available' => $json['data']['Wallets'][$this->getANXPROCode($cur)]['Available_Balance']['value'],
        'daily_withdrawl' => $json['data']['Wallets'][$this->getANXPROCode($cur)]['Daily_Withdrawal_Limit']['value'],
        'max_widthdrawl' => $json['data']['Wallets'][$this->getANXPROCode($cur)]['Max_Withdraw']['value'],
      );
    }
    return $result;

  }

  public function generatePostData($account) {
    $path = 'money/info';

    // generate a nonce as microtime, with as-string handling to avoid problems with 32bits systems
    $mt = explode(' ', microtime());
    $req['nonce'] = $mt[1] . substr($mt[0], 2, 6);

    // generate the POST data string
    $post_data = http_build_query($req, '', '&');

    // generate the extra headers
    $headers = array(
      'Rest-Key: ' . $account['api_key'],
      'Rest-Sign: ' . base64_encode(hash_hmac('sha512', $path . "\0" . $post_data, base64_decode($account['api_secret']), true)),
      'Content-Type: application/x-www-form-urlencoded',
    );

    return array(
      'headers' => $headers,
      'post_data' => $post_data,
    );
  }

}
