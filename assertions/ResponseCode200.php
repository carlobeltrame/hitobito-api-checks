<?php

require_once __DIR__.'/../BaseAssertion.php';
require_once __DIR__.'/../BaseTest.php';
require_once __DIR__.'/../AssertionFailedException.php';

class ResponseCode200 extends BaseAssertion {
  public function __invoke(BaseTest $test) {
    $responseCode = curl_getinfo($test->get_curl(), CURLINFO_HTTP_CODE);
    if ($responseCode !== 200) {
      throw new AssertionFailedException(
        'Unexpected response code.',
        '200 OK',
        self::wrap_in_pre(var_export($responseCode, true))
      );
    }
  }
}
