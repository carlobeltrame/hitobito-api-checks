<?php

require_once __DIR__ . '/BaseTest.php';

abstract class HeadersTest extends BaseTest {

  public function get_name() {
    return 'headers style';
  }

  protected function do_get_request($path, $headers = []) {
    return parent::do_get_request($path, array_merge(['Accept: application/json', 'X-Token: ' . $this->token], $headers));
  }

}
