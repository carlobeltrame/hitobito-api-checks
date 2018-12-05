<?php

require_once __DIR__.'/BaseTest.php';
require_once __DIR__.'/AssertionFailedException.php';

abstract class BaseAssertion {

  /**
   * Test the condition represented by this assertion.
   *
   * @param BaseTest $test the test class instance calling this assertion
   * @return void
   * @throws AssertionFailedException if the condition wasn't met
   */
  abstract function __invoke(BaseTest $test);

  static function wrap_in_pre(string $preformattedText) {
    return '<pre>' . htmlspecialchars($preformattedText) . '</pre>';
  }

}
