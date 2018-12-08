<?php

require_once __DIR__ . '/../ParamsTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsListOfPeople.php';

class GroupMembersParamsTest extends ParamsTest {

  public function get_name() {
    return 'Get people which are members of token\'s group (' . parent::get_name() . ')';
  }

  public function when() {
    $this->do_get_request('/groups/' . $this->groupId . '/people');
  }

  public function then() {
    return [
      new ResponseCode200(),
      new JsonResponse(),
      new ContainsListOfPeople(),
    ];
  }
}
