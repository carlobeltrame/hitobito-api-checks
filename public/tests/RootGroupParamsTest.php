<?php

require_once __DIR__ . '/../BaseTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsSingleGroupDetail.php';

class RootGroupParamsTest extends BaseTest {

  public function get_name() {
    return 'Get root group details (parameters style)';
  }

  public function perform() {
    $this->do_get_with_parameters_style('/groups');
  }

  /**
   * @return array
   */
  public function get_assertions() {
    return [
      new ResponseCode200(),
      new JsonResponse(),
      new ContainsSingleGroupDetail('1'),
    ];
  }
}
