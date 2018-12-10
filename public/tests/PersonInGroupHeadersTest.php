<?php

require_once __DIR__ . '/../HeadersTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsSinglePersonDetail.php';
require_once __DIR__ . '/../assertions/SinglePersonHasRoleInGroup.php';

class PersonInGroupHeadersTest extends HeadersTest {

  protected $personGroupId;
  protected $personId;

  public function get_name() {
    return 'Get a person by id (' . parent::get_name() . ')';
  }

  protected function find_any_person($groupId) {
    $this->do_get_request('/groups/' . $groupId . '/people');
    $body = json_decode($this->get_response_body());
    if (isset($body->people) && is_array($body->people) && count($body->people)) {
      return array($groupId, $body->people[0]->id);
    }

    // No person found, give up
    return array(null, null);
  }

  public function given() {
    parent::given();

    list($this->personGroupId, $this->personId) = $this->find_any_person($this->groupId);

    if ($this->personGroupId === null || $this->personId === null) {
      throw new TestNotApplicableException('No person found that is accessible with this token.');
    }
  }

  public function when() {
    $this->do_get_request('/groups/' . $this->personGroupId . '/people/' . $this->personId);
  }

  public function then() {
    return [
      new ResponseCode200(),
      new JsonResponse(),
      new ContainsSinglePersonDetail($this->personId),
      new SinglePersonHasRoleInGroup($this->personGroupId),
    ];
  }
}
