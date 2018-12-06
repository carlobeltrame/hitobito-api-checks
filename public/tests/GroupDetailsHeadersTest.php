<?php

require_once __DIR__ . '/../BaseTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsSingleGroupDetail.php';

class GroupDetailsHeadersTest extends BaseTest {

  public function get_name() {
    return 'GET /groups/{id} (headers style)';
  }

  public function perform() {
    $this->do_get_with_headers_style('/groups/' . $this->groupId);
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
