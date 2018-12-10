<?php

require_once __DIR__ . '/../HeadersTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsSingleGroupDetail.php';
require_once __DIR__ . '/../assertions/Not.php';
require_once __DIR__ . '/../TestNotApplicableException.php';

class ParentGroupByIdHeadersTest extends HeadersTest {

  protected $parentId;

  public function get_name() {
    return 'Get group details of parent of token\'s group (' . parent::get_name() . ')';
  }

  public function given() {
    parent::given();
    if (!$this->groupsPermission) {
      throw new TestNotApplicableException('Token does not have permission to read the group itself, let alone its parent.');
    }
    $this->do_get_request('/groups/' . $this->groupId);
    $this->parentId = json_decode($this->responseBody)->groups[0]->links->parent;
    if (!$this->parentId) {
      throw new TestNotApplicableException('Token\'s group has no parent because it is the root group.');
    }
  }

  public function when() {
    $this->do_get_request('/groups/' . $this->parentId);
  }

  public function then() {
    return [
      new ResponseCode200(),
      new JsonResponse(),
      new Not(new ContainsSingleGroupDetail($this->parentId)),
    ];
  }
}
