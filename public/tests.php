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

$test = filter_input(INPUT_GET, 'test');
$hitobitoUrl = getParam('hitobitoUrl');
$apiToken = getParam('apiToken');
$groupId = getParam('groupId');

if ($test && $hitobitoUrl && $apiToken && $groupId) {

  require_once $test;
  $classname = basename($test, '.php');
  /** @var BaseTest $testObject */
  $testObject = new $classname($hitobitoUrl, $apiToken, $groupId);
  echo json_encode($testObject->test());

} else {

  echo json_encode(getClassesFromDirectory('tests'));

}
