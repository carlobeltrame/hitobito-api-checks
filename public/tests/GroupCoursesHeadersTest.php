<?php

require_once __DIR__ . '/../HeadersTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsListOfEvents.php';
require_once __DIR__ . '/../assertions/Not.php';

class GroupCoursesHeadersTest extends HeadersTest {

  public function get_name() {
    return 'Get courses of token\'s group (' . parent::get_name() . ')';
  }

  public function when() {
    $this->do_get_request('/groups/' . $this->groupId . '/events/course');
  }

  public function then() {
    if ($this->eventsPermission) {
      return [
        new ResponseCode200(),
        new JsonResponse(),
        new ContainsListOfEvents(),
      ];
    } else {
      return [
        new ResponseCode200(),
        new JsonResponse(),
        new Not(new ContainsListOfEvents()),
      ];
    }
  }
}
