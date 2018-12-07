<?php

require_once __DIR__ . '/../HeadersTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/CorsHeaderPresent.php';

class CorsHeaderHeadersTest extends HeadersTest {

  public function get_name() {
    return 'CORS header on API response (' . parent::get_name() . ')';
  }

  public function when() {
    $this->do_get_request('/groups/' . $this->groupId);
  }

  public function then() {
    return [
      new ResponseCode200(),
      new CorsHeaderPresent(),
    ];
  }
}
