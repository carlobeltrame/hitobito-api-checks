<?php

require_once __DIR__ . '/../BaseTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/CorsHeaderPresent.php';

class CorsHeaderParamsTest extends BaseTest {

  public function get_name() {
    return 'CORS header on API response (parameters style)';
  }

  public function perform() {
    $this->do_get_with_parameters_style('/groups/' . $this->groupId);
  }

  /**
   * @return array
   */
  public function get_assertions() {
    return [
      new ResponseCode200(),
      new CorsHeaderPresent(),
    ];
  }
}
