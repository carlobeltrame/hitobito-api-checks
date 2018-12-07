<?php

require_once __DIR__ . '/../HeadersTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsSingleGroupDetail.php';
require_once __DIR__ . '/../TestNotApplicableException.php';

class ChildGroupByIdHeadersTest extends HeadersTest {

  protected $childId;

  public function get_name() {
    return 'Get group details of parent of token\'s group (' . parent::get_name() . ')';
  }

  public function given() {
    parent::given();
    $this->do_get_request('/groups/' . $this->groupId);
    $children = json_decode($this->responseBody)->groups[0]->links->children;
    if (!is_array($children) || count($children) < 1) {
      throw new TestNotApplicableException('Token\'s group has no child groups.');
    }
    $this->childId = $children[0];
  }

  public function when() {
    $this->do_get_request('/groups/' . $this->childId);
  }

  public function then() {
    // TODO depending on the token's permissions, return different assertions
    return [
      new ResponseCode200(),
      new JsonResponse(),
      new ContainsSingleGroupDetail($this->childId),
    ];
  }
}
