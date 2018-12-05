<?php

class AssertionFailedException extends Exception {

  protected $expected;
  protected $actual;

  public function __construct(string $message = '', $expected = '', $actual = '') {
    parent::__construct($message);
    $this->expected = $expected;
    $this->actual = $actual;
  }

  public function get_expected() {
    return $this->expected;
  }

  public function get_actual() {
    return $this->actual;
  }

}
