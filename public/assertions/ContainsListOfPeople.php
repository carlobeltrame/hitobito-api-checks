<?php

require_once __DIR__ . '/../BaseAssertion.php';
require_once __DIR__ . '/../BaseTest.php';
require_once __DIR__ . '/../AssertionFailedException.php';

class ContainsListOfPeople extends BaseAssertion {

  public function __invoke(BaseTest $test) {

    $json = $test->get_response_body();
    $body = json_decode($json);
    if (!isset($body->people)) {
      throw new AssertionFailedException(
        'Response body does not contain a people list entry.',
        'JSON response containing a people list entry.',
        $json
      );
    }
    if (!is_array($body->people)) {
      throw new AssertionFailedException(
        'people entry in response body is not a list.',
        'JSON list in people entry of response.',
        $json
      );
    }
    foreach ($body->people as $person) {
      if (!isset($person->type) || $person->type !== 'people') {
        throw new AssertionFailedException(
          'people list in response body contains non-people entries.',
          'JSON list containing only people entries.',
          var_export($body->people, true)
        );
      }
    }
  }
}
