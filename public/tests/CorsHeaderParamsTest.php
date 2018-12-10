<?php

require_once __DIR__ . '/../ParamsTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/CorsHeaderPresent.php';

class CorsHeaderParamsTest extends ParamsTest {

  public function get_name() {
    return 'CORS header on API response (' . parent::get_name() . ')';
  }

  public function given() {
    if (!$this->groupsPermission && !$this->peoplePermission && !$this->eventsPermission) {
      throw new TestNotApplicableException('Cannot test CORS header presence without any permissions on token.');
    }
  }

  public function when() {
    if ($this->groupsPermission) {
      $this->do_get_request('/groups/' . $this->groupId);
    } else if ($this->peoplePermission) {
      $this->do_get_request('/groups/' . $this->groupId . '/people');
    } else if ($this->eventsPermission) {
      $this->do_get_request('/groups/' . $this->groupId . '/events');
    }
  }

  public function then() {
    return [
      new ResponseCode200(),
      new CorsHeaderPresent(),
    ];
  }
}
