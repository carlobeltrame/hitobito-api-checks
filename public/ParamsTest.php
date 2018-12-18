<?php

require_once __DIR__ . '/BaseTest.php';

abstract class ParamsTest extends BaseTest {

  public function get_name() {
    return 'params style';
  }

  protected function do_get_request($path, $headers = [], $query = []) {
    $query['token'] = $this->token;
    return parent::do_get_request($path . '.json', $headers, $query);
  }

}
