<?php

require_once __DIR__ . '/../BaseAssertion.php';
require_once __DIR__ . '/../BaseTest.php';
require_once __DIR__ . '/../AssertionFailedException.php';

class CorsHeaderPresent extends BaseAssertion {
  public function __invoke(BaseTest $test) {
    $headers = $test->get_response_headers();
    if (!isset($headers['access-control-allow-origin'])) {
      throw new AssertionFailedException(
        'Access-Control-Allow-Origin header was missing on the response.',
        'Access-Control-Allow-Origin: <caller domain if allowed>'
      );
    }
  }
}
