<?php

require_once __DIR__ . '/../BaseAssertion.php';
require_once __DIR__ . '/../BaseTest.php';
require_once __DIR__ . '/../AssertionFailedException.php';

class ContainsSingleEventDetail extends BaseAssertion {

  protected $eventId;

  public function __construct($eventId) {
    $this->eventId = $eventId;
  }

  public function __invoke(BaseTest $test) {

    $json = $test->get_response_body();
    $body = json_decode($json);
    if (!isset($body->events)) {
      throw new AssertionFailedException(
        'Response body does not contain an events entry.',
        'JSON response containing an events entry.',
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
    if (count($body->events) !== 1) {
      throw new AssertionFailedException(
        'events entry in response body does not contain exactly one element.',
        'JSON list in events entry of response containing exactly one element.',
        $json
      );
    }

    $eventDetail = $body->events[0];
    if (!isset($eventDetail->type)) {
      throw new AssertionFailedException(
        'Event details does not contain a type entry.',
        'JSON object containing a type entry.',
        var_export($eventDetail, true)
      );
    }
    if ($eventDetail->type !== 'events') {
      throw new AssertionFailedException(
        'Type inside group details didn\'t match expected value.',
        'events',
        var_export($eventDetail->type)
      );
    }
    if (!isset($eventDetail->id)) {
      throw new AssertionFailedException(
        'Event details should contain an id entry.',
        'JSON object containing an id entry.',
        var_export($eventDetail, true)
      );
    }
    if ($eventDetail->id !== $this->eventId) {
      throw new AssertionFailedException(
        'Id inside event details didn\'t match expected value.',
        $this->eventId,
        var_export($eventDetail->id)
      );
    }
  }
}
