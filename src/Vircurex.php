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
 * Represents the Vircurex exchange wallet.
 */
class Vircurex extends AbstractWallet {

  public function getName() {
    return "Vircurex";
  }

  public function getCode() {
    return "vircurex";
  }

  public function getURL() {
    return "https://vircurex.com/";
  }

  public function getFields() {
    return array(
      'api_username' => array(
        'title' => "Username",
        'regexp' => '#^.+$#',
      ),
      'api_secret' => array(
        'title' => "API Secret",
        'regexp' => "#^.+$#",
      ),
    );
  }

  /**
   * Convert the given openclerk/currencies code into a Vircurex currency code.
   */
  function getVircurexCode($cur) {
    switch ($cur) {
      case "bc1": return "BC";
      case "dog": return "DOGE";

      default:
        return strtoupper($cur);
    }
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {

    // we could get this from get_info_for_currency.json but then we'd
    // really be hammering the server when requesting balances

    return array('anc', 'aur', 'bc1', 'btc', 'dgc', 'dog', 'dvc', 'frc',
        'ftc', 'i0c', 'ixc', 'ltc', 'nmc', 'ppc', 'qrk', 'trc', 'vtc',
        'wdc', 'xpm', 'zet');

  }

  /**
   * @return all account balances
   * @throws AccountFetchException if something bad happened
   */
  public function fetchBalances($account, CurrencyFactory $factory, Logger $logger) {

    $vircurex_balance_count = rand(0,0xffff);
    $result = array();

    foreach ($this->fetchSupportedCurrencies($factory, $logger) as $cur) {
      $currency = $this->getVircurexCode($cur);
      $timestamp = gmdate('Y-m-d\\TH:i:s'); // UTC time
      $id = md5(time() . "_" . rand(0,9999) . "_" . $vircurex_balance_count++);
      $token = hash('sha256', $account['api_secret'] . ";" . $account['api_username'] . ";" . $timestamp . ";" . $id . ";" . "get_balance" . ";" . $currency);
      $url = "https://api.vircurex.com/api/get_balance.json?account=" . urlencode($account['api_username']) . "&id=" . urlencode($id) . "&token=" . urlencode($token) . "&timestamp=" . urlencode($timestamp) . "&currency=" . urlencode($currency);

      $logger->info($url);
      $this->throttle($logger);

      $raw = Fetch::get($url);

      $json = Fetch::jsonDecode($raw);
      if (isset($json['statustxt']) && $json['statustxt']) {
        throw new AccountFetchException($json['statustxt'] . " for currency $currency");
      }
      if (isset($json['statustext']) && $json['statustext']) {
        throw new AccountFetchException($json['statustext'] . " for currency $currency");
      }

      $result[$cur] = array(
        'confirmed' => $json['balance'],
        'available' => $json['availablebalance'],
      );
    }

    return $result;

  }

}
