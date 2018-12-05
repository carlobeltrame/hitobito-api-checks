<?php

abstract class BaseTest {

  protected $url;
  protected $token;
  protected $groupId;
  protected $curl;
  protected $success = true;

  public function __construct($url, $token, $tokenGroupId, $curl) {
    $this->url = $url;
    $this->token = $token;
    $this->groupId = $tokenGroupId;
    $this->curl = $curl;
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

  protected function do_get_with_headers_style($path) {
    return $this->do_get_request($path, ['Accept: application/json', 'X-Token: ' . $this->token]);
  }

  protected function do_get_with_parameters_style($path, $queryParams = []) {
    $query = implode(array_map(function($param, $value) { return '&' . $param . '=' . $value; }, array_keys($queryParams), $queryParams));
    return $this->do_get_request($path . '.json?token=' . $this->token . $query);
  }

  protected function do_get_request($path, $headers = []) {

    curl_setopt($this->curl, CURLOPT_URL, $this->url . $path);
    curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($this->curl);

    return $result;
  }

}
