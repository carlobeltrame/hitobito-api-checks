<?php

require_once __DIR__ . '/../BaseAssertion.php';
require_once __DIR__ . '/../BaseTest.php';
require_once __DIR__ . '/../AssertionFailedException.php';

class Not extends BaseAssertion {

  protected $wrapped;

  public function __construct(BaseAssertion $wrapped) {
    $this->wrapped = $wrapped;
  }

  public function __invoke(BaseTest $test) {

    try {
      $this->wrapped->__invoke($test);
    } catch (AssertionFailedException $e) {
      // Expect to get here
      return;
    }

    // Wrapped assertion didn't throw, something went wrong
    throw new AssertionFailedException(
      get_class($this->wrapped) . ' should have failed but didn\'t.'
    );
  }
}
