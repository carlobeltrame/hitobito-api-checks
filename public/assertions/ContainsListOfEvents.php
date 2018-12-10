<?php

require_once __DIR__ . '/../BaseAssertion.php';
require_once __DIR__ . '/../BaseTest.php';
require_once __DIR__ . '/../AssertionFailedException.php';

class ContainsListOfEvents extends BaseAssertion {

  public function __invoke(BaseTest $test) {

    $json = $test->get_response_body();
    $body = json_decode($json);
    if (!isset($body->events)) {
      throw new AssertionFailedException(
        'Response body does not contain an events list entry.',
        'JSON response containing an events list entry.',
        $json
      );
    }
    if (!is_array($body->events)) {
      throw new AssertionFailedException(
        'events entry in response body is not a list.',
        'JSON list in events entry of response.',
        $json
      );
    }
    foreach ($body->events as $event) {
      if (!isset($event->type) || $event->type !== 'events') {
        throw new AssertionFailedException(
          'events list in response body contains non-event entries.',
          'JSON list containing only events entries.',
          var_export($body->events, true)
        );
      }
    }
  }
}
