<?php

require_once __DIR__ . '/../ParamsTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsSingleGroupDetail.php';
require_once __DIR__ . '/../assertions/Not.php';

class GroupByIdParamsTest extends ParamsTest {

  public function get_name() {
    return 'Get group details of token\'s group (' . parent::get_name() . ')';
  }

  public function when() {
    $this->do_get_request('/groups/' . $this->groupId);
  }

  public function then() {
    if ($this->groupsPermission) {
      return [
        new ResponseCode200(),
        new JsonResponse(),
        new ContainsSingleGroupDetail($this->groupId),
      ];
    } else {
      return [
        new ResponseCode200(),
        new JsonResponse(),
        new Not(new ContainsSingleGroupDetail($this->groupId))
      ];
    }
  }
}
