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
 * Represents the CEX.io exchange wallet.
 */
class CEXio extends AbstractWallet {

  public function getName() {
    return "CEX.io";
  }

  function getCode() {
    return "cexio";
  }

  function getURL() {
    return "https://cex.io/";
  }

  public function getFields() {
    return array(
      'api_username' => array(
        'title' => "Username",
        'regexp' => '#.+#',
      ),
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#^[A-Za-z0-9]{20,32}$#',
      ),
      'api_secret' => array(
        'title' => "API Secret",
        'regexp' => "#^[A-Za-z0-9]{20,32}$#",
      ),
    );
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {
    // there is no public API to list supported currencies
    return array(
      'btc', 'usd', 'eur', 'ghs', 'drk', 'dog',
      'ltc', 'nmc', 'ixc', 'pot', 'anc', 'mec',
      'wdc', 'ftc', 'myr', 'aur',
    );
  }

  function getCEXioCode($str) {
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

    $url = "https://cex.io/api/balance/";
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
      $code = $this->getCEXioCode($cur);
      if (isset($json[$code])) {
        $result[$cur] = array(
          'available' => $json[$code]['available'],
        );

        if (isset($json[$code]['orders'])) {
          $result[$cur]['reserved'] = $json[$code]['orders'];
          $result[$cur]['confirmed'] = $result[$cur]['available'] + $result[$cur]['reserved'];
        } else {
          $result[$cur]['confirmed'] = $result[$cur]['available'];
        }
      }
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
