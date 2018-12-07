<?php

require_once __DIR__ . '/../HeadersTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsSingleGroupDetail.php';
require_once __DIR__ . '/../assertions/Not.php';

class ParentGroupByIdHeadersTest extends HeadersTest {

  protected $parentId;

  public function get_name() {
    return 'Get group details of parent of token\'s group (' . parent::get_name() . ')';
  }

  public function perform() {
    $this->do_get_request('/groups/' . $this->groupId);
    $this->parentId = json_decode($this->responseBody)->groups[0]->links->parent;
    $this->do_get_request('/groups/' . $this->parentId);
  }

  /**
   * @return array
   */
  public function get_assertions() {
    return [
      new ResponseCode200(),
      new JsonResponse(),
      new Not(new ContainsSingleGroupDetail($this->parentId)),
    ];
  }
}
