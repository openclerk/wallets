<?php

namespace Account\Wallet;

use \Monolog\Logger;
use \Account\Account;
use \Account\Miner;
use \Account\DisabledAccount;
use \Account\SimpleAccountType;
use \Account\AccountFetchException;
use \Apis\FetchException;
use \Apis\FetchHttpException;
use \Apis\Fetch;
use \Openclerk\Currencies\CurrencyFactory;

/**
 * Basic wallet type.
 */
abstract class AbstractWallet extends SimpleAccountType {

  /**
   * Fetch the JSON from the given GET URL, or throw a
   * {@Link AccountFetchException} if something bad happened.
   * @param $throttle the default delay, or 3 seconds if not specified
   */
  function fetchJSON($url, Logger $logger, $throttle = 3) {
    $logger->info($url);

    try {
      $this->throttle($logger, $throttle);
      $raw = Fetch::get($url);
    } catch (FetchHttpException $e) {
      throw new AccountFetchException($e->getContent(), $e);
    }

    try {
      $json = $this->jsonDecode($raw);
    } catch (FetchException $e) {
      $message = strlen($raw) < 64 ? $e->getMessage() : $raw;
      throw new AccountFetchException($message, $e);
    }

    return $json;
  }

  /**
   * By default, calls {@link Fetch#jsonDecode()}
   * @throws FetchException if the JSON could not be read
   */
  function jsonDecode($raw) {
    return Fetch::jsonDecode($raw);
  }

}
