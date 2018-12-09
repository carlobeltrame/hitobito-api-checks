<?php

require_once __DIR__ . '/../BaseAssertion.php';
require_once __DIR__ . '/../BaseTest.php';
require_once __DIR__ . '/../AssertionFailedException.php';

class SinglePersonHasRoleInGroup extends BaseAssertion {

  protected $groupId;

  public function __construct($groupId) {
    $this->groupId = $groupId;
  }

  public function __invoke(BaseTest $test) {

    $json = $test->get_response_body();
    $body = json_decode($json);
    $personDetail = $body->people[0];
    if (!isset($personDetail->links)) {
      throw new AssertionFailedException(
        'Person details should contain a links entry.',
        'JSON object containing a links entry.',
        var_export($personDetail, true)
      );
    }
    if (!isset($personDetail->links->roles)) {
      throw new AssertionFailedException(
        'Person links should contain a roles entry.',
        'JSON object containing a roles entry in the links object.',
        var_export($personDetail, true)
      );
    }
    if (!is_array($personDetail->links->roles)) {
      throw new AssertionFailedException(
        'Roles entry in person\'s links should be an array.',
        'JSON array in the roles entry of the links object.',
        var_export($personDetail->links->roles, true)
      );
    }

    if (!isset($body->linked)) {
      throw new AssertionFailedException(
        'Response body does not contain a linked entry.',
        'JSON response containing a linked entry.',
        $json
      );
    }
    if (!isset($body->linked->roles)) {
      throw new AssertionFailedException(
        'linked entry in response body does not contain a roles entry.',
        'JSON response containing a linked entry with a nested roles entry.',
        $json
      );
    }
    $linkedRoles = $body->linked->roles;
    if (!is_array($linkedRoles)) {
      throw new AssertionFailedException(
        'linked entry in response body is not a JSON array.',
        'JSON response containing a linked entry with a nested roles array.',
        $json
      );
    }

    foreach ($personDetail->links->roles as $linkedRoleId) {
      foreach ($linkedRoles as $linkedRole) {
        if (!isset($linkedRole->id)) {
          throw new AssertionFailedException(
            'Linked role does not have an id.',
            'JSON object containing an id field.',
            var_export($linkedRole, true));
        }
        if (!isset($linkedRole->links)) {
          throw new AssertionFailedException(
            'Linked role does not have a links entry.',
            'JSON object containing a links field.',
            var_export($linkedRole, true));
        }
        if (!isset($linkedRole->links->group)) {
          throw new AssertionFailedException(
            'Linked role\'s links object does not have a group entry.',
            'JSON object containing a group field.',
            var_export($linkedRole->links, true));
        }
        if ($linkedRole->id === $linkedRoleId) {
          if ($linkedRole->links->group === $this->groupId) {
            return;
          }
        }
      }
    }

    throw new AssertionFailedException(
      'Returned person does not have a role in group ' . $this->groupId,
      'Person entity with a role in group ' . $this->groupId,
      $json
    );
  }
}
