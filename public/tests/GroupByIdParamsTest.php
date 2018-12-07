<?php

require_once __DIR__ . '/../ParamsTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsSingleGroupDetail.php';

class GroupDetailsParamsTest extends ParamsTest {

  public function get_name() {
    return 'Get group details of token\'s group (' . parent::get_name() . ')';
  }

  public function perform() {
    $this->do_get_request('/groups/' . $this->groupId);
  }

  /**
   * @return array
   */
  public function get_assertions() {
    return [
      new ResponseCode200(),
      new JsonResponse(),
      new ContainsSingleGroupDetail($this->groupId),
    ];
  }
}
