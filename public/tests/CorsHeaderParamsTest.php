<?php

require_once __DIR__ . '/../ParamsTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/CorsHeaderPresent.php';

class CorsHeaderParamsTest extends ParamsTest {

  public function get_name() {
    return 'CORS header on API response (' . parent::get_name() . ')';
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
      new CorsHeaderPresent(),
    ];
  }
}
