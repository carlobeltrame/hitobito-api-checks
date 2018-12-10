<?php

require_once __DIR__ . '/../BaseAssertion.php';
require_once __DIR__ . '/../BaseTest.php';
require_once __DIR__ . '/../AssertionFailedException.php';

class ContainsSinglePersonDetail extends BaseAssertion {

  protected $personId;

  public function __construct($personId) {
    $this->personId = $personId;
  }

  public function __invoke(BaseTest $test) {

    $json = $test->get_response_body();
    $body = json_decode($json);
    if (!isset($body->people)) {
      throw new AssertionFailedException(
        'Response body does not contain a people entry.',
        'JSON response containing a people entry.',
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
    if (count($body->people) !== 1) {
      throw new AssertionFailedException(
        'people entry in response body does not contain exactly one element.',
        'JSON list in people entry of response containing exactly one element.',
        $json
      );
    }

    $personDetail = $body->people[0];
    if (!isset($personDetail->type)) {
      throw new AssertionFailedException(
        'Person details does not contain a type entry.',
        'JSON object containing a type entry.',
        var_export($personDetail, true)
      );
    }
    if ($personDetail->type !== 'people') {
      throw new AssertionFailedException(
        'Type inside group details didn\'t match expected value.',
        'people',
        var_export($personDetail->type)
      );
    }
    if (!isset($personDetail->id)) {
      throw new AssertionFailedException(
        'Person details should contain an id entry.',
        'JSON object containing an id entry.',
        var_export($personDetail, true)
      );
    }
    if ($personDetail->id !== $this->personId) {
      throw new AssertionFailedException(
        'Id inside person details didn\'t match expected value.',
        $this->personId,
        var_export($personDetail->id)
      );
    }
  }
}
