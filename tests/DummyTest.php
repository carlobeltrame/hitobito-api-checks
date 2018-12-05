<?php

require_once __DIR__.'/../BaseTest.php';

class DummyTest extends BaseTest {

  public function get_name() {
    return 'dummy test';
  }

  public function perform() {
    $this->do_get_with_headers_style('/groups/' . $this->groupId);
  }

  /**
   * @return array
   */
  public function get_assertions() {
    return [];
  }

}
