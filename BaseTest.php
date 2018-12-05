<?php

abstract class BaseTest {

  protected $url;
  protected $token;
  protected $groupId;
  protected $curl;
  protected $responseHeaders = [];
  protected $responseBody = null;
  protected $message = '';
  protected $expected = '';
  protected $actual = '';
  protected $success = true;

  public function __construct($url, $token, $tokenGroupId, $curl) {
    $this->url = $url;
    $this->token = $token;
    $this->groupId = $tokenGroupId;
    $this->curl = $curl;
  }

  public function render() {
    echo '<a class="panel-block">';
    if ($this->is_success()) {
      echo '<span class="panel-icon has-text-success"><i class="fas fa-check-circle"></i></span>';
    } else {
      echo '<span class="panel-icon has-text-danger"><i class="fas fa-times-circle"></i></span>';
    }
    echo '<div>';
    echo '<div class="title is-6">' . $this->get_name() . '</div>';

    if ($this->success) {
      echo 'Works as expected.';
    } else {
      echo $this->message;
      if ($this->expected !== '') {
        echo '<div><b>Expected:</b> ' . $this->expected . '</div>';
      }
      if ($this->actual !== '') {
        echo '<div><b>Actual:</b> ' . $this->actual . '</div>';
      }
    }

    echo '</div></a>';
  }

  public abstract function get_name();

  public function is_success() {
    return $this->success;
  }

  public function get_response_headers() {
    return $this->responseHeaders;
  }

  public function get_response_body() {
    return $this->responseBody;
  }

  public abstract function perform();

  /**
   * @return array
   */
  public abstract function get_assertions();

  public function evaluate() {
    $this->success = true;
    try {
      $this->perform();
      foreach ($this->get_assertions() as $assertion) {
        /** @var $assertion BaseAssertion */
        $assertion($this);
      }
    } catch (AssertionFailedException $e) {
      $this->success = false;
      $this->message = $e->getMessage();
      $this->expected = $e->get_expected();
      $this->actual = $e->get_actual();
    } catch (Exception $e) {
      $this->success = false;
      $this->message = $e->getMessage();
    }
  }

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

    curl_setopt($this->curl, CURLOPT_HEADERFUNCTION,
      function($curl, $header)
      {
        $len = strlen($header);
        $header = explode(':', $header, 2);
        if (count($header) < 2) // ignore invalid headers
          return $len;

        $name = strtolower(trim($header[0]));
        if (!array_key_exists($name, $this->responseHeaders))
          $this->responseHeaders[$name] = [trim($header[1])];
        else
          $this->responseHeaders[$name][] = trim($header[1]);

        return $len;
      }
    );

    $this->responseBody = curl_exec($this->curl);

    return $this->responseBody;
  }

}
