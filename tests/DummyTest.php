<?php

require_once __DIR__.'/../BaseTest.php';

class DummyTest extends BaseTest {

  public function __construct($url, $token, $tokenGroupId, $curl) {
    parent::__construct($url, $token, $tokenGroupId, $curl);

    $this->success = ($this->do_get_with_headers_style('/groups/' . $this->groupId) !== false);
  }

  public function get_name() {
    return 'dummy test';
  }

  public function render() {
    echo 'success!';
  }
}
