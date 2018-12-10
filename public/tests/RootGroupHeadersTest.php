<?php

require_once __DIR__ . '/../HeadersTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsSingleGroupDetail.php';
require_once __DIR__ . '/../assertions/Not.php';

class RootGroupHeadersTest extends HeadersTest {

  public function get_name() {
    return 'Get root group details (' . parent::get_name() . ')';
  }

  public function when() {
    $this->do_get_request('/groups');
  }

  public function then() {
    if ($this->groupsPermission) {
      return [
        new ResponseCode200(),
        new JsonResponse(),
        new ContainsSingleGroupDetail('1'),
      ];
    } else {
      return [
        new ResponseCode200(),
        new JsonResponse(),
        new Not(new ContainsSingleGroupDetail('1')),
      ];
    }
  }
}
