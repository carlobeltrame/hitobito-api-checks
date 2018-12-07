<?php

require_once __DIR__ . '/../HeadersTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsSingleGroupDetail.php';
require_once __DIR__ . '/../assertions/Not.php';

class RootGroupByIdHeadersTest extends HeadersTest {

  public function get_name() {
    return 'Get root group details by specifying id (' . parent::get_name() . ')';
  }

  public function when() {
    $this->do_get_request('/groups/1');
  }

  public function then() {
    // If the token is on the root group, we should be granted access
    if ($this->groupId === '1') {
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
