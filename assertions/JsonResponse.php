<?php

require_once __DIR__.'/../BaseAssertion.php';
require_once __DIR__.'/../BaseTest.php';
require_once __DIR__.'/../AssertionFailedException.php';

class JsonResponse extends BaseAssertion {
  public function __invoke(BaseTest $test) {
    $body = $test->get_response_body();
    if (json_decode($body) === null && trim($body) !== 'null') {
      throw new AssertionFailedException(
        'Response body is not valid JSON.',
        'Valid JSON response.',
        self::wrap_in_pre($body)
      );
    }
  }
}
