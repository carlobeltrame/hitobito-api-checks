<?php

require_once __DIR__ . '/../ParamsTest.php';
require_once __DIR__ . '/../assertions/ResponseCode200.php';
require_once __DIR__ . '/../assertions/JsonResponse.php';
require_once __DIR__ . '/../assertions/ContainsSinglePersonDetail.php';
require_once __DIR__ . '/../assertions/SinglePersonHasRoleInGroup.php';
require_once __DIR__ . '/../assertions/Not.php';

class PersonInChildGroupParamsTest extends ParamsTest {

  protected $personGroupId;
  protected $personId;

  public function get_name() {
    return 'Get a person in a child group by id (' . parent::get_name() . ')';
  }

  protected function find_any_person_in_subgroups($groupId) {
    if ($this->peopleBelowPermission) {
      $this->do_get_request('/groups/' . $groupId);
      $body = json_decode($this->get_response_body());
      if (isset($body->groups) && is_array($body->groups) && count($body->groups)) {
        $group = $body->groups[0];
        if (isset($group->links) && isset($group->links->children) && is_array($group->links->children)) {
          foreach ($group->links->children as $subgroup) {
            list($groupIdWithPerson, $personId) = $this->find_any_person($subgroup);
            if ($groupIdWithPerson !== null && $personId !== null) {
              return array($groupIdWithPerson, $personId);
            }
          }
        }
      }
    }
    return array(null, null);
  }

  protected function find_any_person($groupId) {
    $this->do_get_request('/groups/' . $groupId . '/people');
    $body = json_decode($this->get_response_body());
    if (isset($body->people) && is_array($body->people) && count($body->people)) {
      return array($groupId, $body->people[0]->id);
    }

    list($grouIdWithPerson, $personId) = $this->find_any_person_in_subgroups($groupId);
    if ($grouIdWithPerson !== null && $personId !== null) {
      return array($grouIdWithPerson, $personId);
    }

    // No person found, give up
    return array(null, null);
  }

  public function given() {
    parent::given();

    if (!$this->groupsPermission) {
      throw new TestNotApplicableException('Cannot find child group ids without the groups permission.');
    }

    list($this->personGroupId, $this->personId) = $this->find_any_person_in_subgroups($this->groupId);

    if ($this->personGroupId === null || $this->personId === null) {
      throw new TestNotApplicableException('No person found in child groups that is accessible with this token.');
    }
  }

  public function when() {
    $this->do_get_request('/groups/' . $this->personGroupId . '/people/' . $this->personId);
  }

  public function then() {
    if ($this->peopleBelowPermission) {
      return [
        new ResponseCode200(),
        new JsonResponse(),
        new ContainsSinglePersonDetail($this->personId),
        new SinglePersonHasRoleInGroup($this->personGroupId),
      ];
    } else {
      return [
        new ResponseCode200(),
        new JsonResponse(),
        new Not(new ContainsSinglePersonDetail($this->personId)),
      ];
    }
  }
}
