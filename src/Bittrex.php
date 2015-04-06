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
 * Represents the Bittrex exchange wallet.
 */
class Bittrex extends AbstractWallet {

  public function getName() {
    return "Bittrex";
  }

  function getCode() {
    return "bittrex";
  }

  function getURL() {
    return "https://bittrex.com/";
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

  /**
   * Convert the given Bittrex currency code (uppercase)
   * into the openclerk/currencies currency code (lowercase, three characters)
   * so that it can be used in openclerk/currencies
   */
  function getCurrencyCode($str) {
    switch (strtoupper($str)) {
      // exceptions
      case "DOGE": return "dog";

      // otherwise return lowercase
      default:
        return strtolower($str);
    }
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {
    $url = "https://bittrex.com/api/v1.1/public/getmarketsummaries";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    if (!$json['success']) {
      throw new AccountFetchException($json['message']);
    }

    $result = array();

    foreach ($json['result'] as $market) {
      $pairs = explode("-", $market['MarketName'], 2);

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

    try {
      $this->throttle($logger);
      $data = $this->generateGetData("https://bittrex.com/api/v1.1/account/getbalances", $account);
      $logger->info($data['url']);
      $raw = Fetch::get($data['url'], array(), $data['headers']);
    } catch (FetchHttpException $e) {
      throw new AccountFetchException($e->getMessage(), $e);
    }

    $json = Fetch::jsonDecode($raw);
    if (!$json['success']) {
      throw new AccountFetchException($json['message']);
    }

    $result = array();
    foreach ($json['result'] as $info) {
      $cur = $this->getCurrencyCode($info['Currency']);
      $result[$cur] = array(
        'confirmed' => $info['Balance'],
        'available' => $info['Available'],
        'unconfirmed' => $info['Pending'],
      );
    }

    return $result;

  }

  public function generateGetData($path, $account) {
    $nonce = time();
    $path = $path . "?apikey=" . urlencode($account['api_key']) . "&nonce=" . urlencode($nonce);
    $sign = hash_hmac('sha512', $path, $account['api_secret']);

    // generate the extra headers
    $headers = array(
      'apisign: ' . $sign,
    );

    return array(
      'url' => $path,
      'headers' => $headers,
    );
  }

}
