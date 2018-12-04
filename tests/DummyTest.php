<?php

class DummyTest extends BaseTest {

  public function __construct($url, $token, $tokenGroupId) {
    parent::__construct($url, $token, $tokenGroupId);

    $this->success = ($this->do_get_request('/groups') === null);
  }

  public function get_name() {
    return 'dummy test';
  }

  public function render() {
    echo 'success!';
  }
}
