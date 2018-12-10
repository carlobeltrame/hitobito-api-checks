<?php

require_once __DIR__ . '/../HeadersTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsListOfPeople.php';
require_once __DIR__ . '/../assertions/Not.php';

class GroupMembersHeadersTest extends HeadersTest {

  public function get_name() {
    return 'Get people which are members of token\'s group (' . parent::get_name() . ')';
  }

  public function when() {
    $this->do_get_request('/groups/' . $this->groupId . '/people');
  }

  public function then() {
    if ($this->peoplePermission) {
      return [
        new ResponseCode200(),
        new JsonResponse(),
        new ContainsListOfPeople(),
      ];
    } else {
      return [
        new ResponseCode200(),
        new JsonResponse(),
        new Not(new ContainsListOfPeople()),
      ];
    }
  }
}
