<?php

require_once __DIR__ . '/BaseTest.php';
require_once __DIR__ . '/AssertionFailedException.php';

abstract class BaseAssertion {

  /**
   * Test the condition represented by this assertion.
   *
   * @param BaseTest $test the test class instance calling this assertion
   * @return void
   * @throws AssertionFailedException if the condition wasn't met
   */
  abstract function __invoke(BaseTest $test);

}
