<?php

require_once __DIR__ . '/../HeadersTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsSingleEventDetail.php';

class EventOfGroupHeadersTest extends HeadersTest {

  protected $eventGroupId;
  protected $eventId;

  public function get_name() {
    return 'Get an event by id (' . parent::get_name() . ')';
  }

  protected function find_any_event($groupId) {
    if ($this->eventsPermission) {
      $this->do_get_request('/groups/' . $groupId . '/events', [], [ 'start_date' => '01-01-2000' ]);
      $body = json_decode($this->get_response_body());
      if (isset($body->events) && is_array($body->events) && count($body->events)) {
        $event = $body->events[0];
        if (isset($event->links) && isset($event->links->groups) && is_array($event->links->groups) && count($event->links->groups)) {
          return array($event->links->groups[0], $event->id);
        }
      }
    }

    // No event found, give up
    return array(null, null);
  }

  public function given() {
    parent::given();

    list($this->eventGroupId, $this->eventId) = $this->find_any_event($this->groupId);

    if ($this->eventGroupId === null || $this->eventId === null) {
      throw new TestNotApplicableException('No event found that is accessible with this token.');
    }
  }

  public function when() {
    $this->do_get_request('/groups/' . $this->eventGroupId . '/events/' . $this->eventId);
  }

  public function then() {
    return [
      new ResponseCode200(),
      new JsonResponse(),
      new ContainsSingleEventDetail($this->eventId),
    ];
  }
}
