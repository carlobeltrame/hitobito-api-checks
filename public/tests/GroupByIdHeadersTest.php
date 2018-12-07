<?php

require_once __DIR__ . '/../HeadersTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsSingleGroupDetail.php';

class GroupByIdHeadersTest extends HeadersTest {

  public function get_name() {
    return 'Get group details of token\'s group (' . parent::get_name() . ')';
  }

  public function when() {
    $this->do_get_request('/groups/' . $this->groupId);
  }

  public function then() {
    return [
      new ResponseCode200(),
      new JsonResponse(),
      new ContainsSingleGroupDetail($this->groupId),
    ];
  }
}