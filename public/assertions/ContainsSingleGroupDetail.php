<?php

require_once __DIR__ . '/../BaseAssertion.php';
require_once __DIR__ . '/../BaseTest.php';
require_once __DIR__ . '/../AssertionFailedException.php';

class ContainsSingleGroupDetail extends BaseAssertion {

  protected $groupId;

  public function __construct($groupId) {
    $this->groupId = $groupId;
  }

  public function __invoke(BaseTest $test) {

    $json = $test->get_response_body();
    $body = json_decode($json);
    if (!isset($body->groups)) {
      throw new AssertionFailedException(
        'Response body does not contain a groups entry.',
        'JSON response containing a group entry.',
        $json
      );
    }
    if (!is_array($body->groups)) {
      throw new AssertionFailedException(
        'groups entry in response body is not a list.',
        'JSON list in group entry of response.',
        $json
      );
    }
    if (count($body->groups) !== 1) {
      throw new AssertionFailedException(
        'groups entry in response body does not contain exactly one element.',
        'JSON list in group entry of response containing exactly one element.',
        $json
      );
    }

    $groupDetail = $body->groups[0];
    if (!isset($groupDetail->id)) {
      throw new AssertionFailedException(
        'Group details should contain an id entry.',
        'JSON object containing an id entry.',
        var_export($groupDetail, true)
      );
    }
    if ($groupDetail->id !== $this->groupId) {
      throw new AssertionFailedException(
        'Id inside group details didn\'t match expected value.',
        $this->groupId,
        var_export($groupDetail->id)
      );
    }
  }
}
