<?php

require_once __DIR__ . '/../ParamsTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsSingleGroupDetail.php';
require_once __DIR__ . '/../assertions/Not.php';

class RootGroupByIdParamsTest extends ParamsTest {

  public function get_name() {
    return 'Get root group details by specifying id (' . parent::get_name() . ')';
  }

  public function perform() {
    $this->do_get_request('/groups/1');
  }

  /**
   * @return array
   */
  public function get_assertions() {
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
