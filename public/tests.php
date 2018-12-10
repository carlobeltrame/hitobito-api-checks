<?php

function getParam($paramName) {
  return filter_input(INPUT_GET, $paramName) ?: '';
};

function getClassesFromDirectory($directory) {
  $files = new FileSystemIterator($directory, FileSystemIterator::SKIP_DOTS);
  $classes = [];
  foreach ($files as $file) {
    $classes[] = $file->getPathname();
  }
  sort($classes);
  return $classes;
};

$test = getParam('test');
$hitobitoUrl = getParam('hitobitoUrl');
$apiToken = getParam('apiToken');
$groupId = getParam('groupId');
$peoplePermission = (getParam('people') !== 'false');
$peopleBelowPermission = (getParam('peopleBelow') !== 'false');
$groupsPermission = (getParam('groups') !== 'false');
$eventsPermission = (getParam('events') !== 'false');

if ($test && $hitobitoUrl && $apiToken && $groupId) {

  // Check that we only access tests in the test directory
  $testDir = realpath(__DIR__ . '/tests');
  $testPath = realpath($test);
  if (substr($testPath, 0, strlen($testDir)) === $testDir) {

    require_once $test;
    $classname = basename($test, '.php');
    /** @var BaseTest $testInstance */
    $testInstance = new $classname($hitobitoUrl, $apiToken, $groupId,
      $peoplePermission, $peopleBelowPermission, $groupsPermission, $eventsPermission,
      $test);
    echo json_encode($testInstance->test());

    return;

  }

}

echo json_encode(getClassesFromDirectory('tests'));
