<?php

abstract class BaseTest {

  protected $url;
  protected $token;
  protected $groupId;
  protected $success = true;

  public function __construct($url, $token, $tokenGroupId) {
    $this->url = $url;
    $this->token = $token;
    $this->groupId = $tokenGroupId;
  }

  public function __invoke() {
    echo '<a class="panel-block">';
    if ($this->is_success()) {
      echo '<span class="panel-icon has-text-success"><i class="fas fa-check-circle"></i></span>';
    } else {
      echo '<span class="panel-icon has-text-danger"><i class="fas fa-times-circle"></i></span>';
    }
    echo '<div>';
    echo '<div class="title is-6">' . $this->get_name() . '</div>';

    $this->render();

    echo '</div></a>';
  }

  public abstract function get_name();

  public function is_success() {
    return $this->success;
  }

  public abstract function render();

  protected function do_get_request($path) {
    // TODO implement HTTP call
    return null;
  }

}
