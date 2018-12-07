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
  protected $reproduce = [];
  protected $success = true;

  public function __construct($url, $token, $tokenGroupId) {
    $this->url = $url;
    $this->token = $token;
    $this->groupId = $tokenGroupId;
    $this->curl = curl_init();
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

  public function get_curl() {
    return $this->curl;
  }

  /**
   * Perform any necessary preparations for the test. This can include gathering information via API calls. Requests in
   * this method will not be logged to the reproduction instructions.
   *
   * @return void
   */
  public function given() {}

  /**
   * Perform the action on the API which should be tested. This will include at least one API call normally. All API
   * calls via do_get_request are logged automatically to the reproduction instructions.
   *
   * @return void
   */
  public abstract function when();

  /**
   * Returns an array of BaseAssertion instances. All assertions will be evaluated in order.
   *
   * @return array
   */
  public abstract function then();

  public function test() {
    $this->success = true;
    try {
      $this->given();
    } catch (Exception $e) {
      $this->success = false;
      $this->message = 'Test setup failed: ' . $e->getMessage();
      $this->expected = '';
      $this->actual = '';
      $this->reproduce = [];
      return $this->to_response();
    }
    // Reset reproduction steps
    $this->reproduce = [];
    try {
      $this->when();
    } catch (Exception $e) {
      $this->success = false;
      $this->message = 'Test execution failed: ' . $e->getMessage();
      $this->expected = '';
      $this->actual = '';
      return $this->to_response();
    }
    try {
      foreach ($this->then() as $assertion) {
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
      $this->message = 'Assertion failed. ' . $e->getMessage();
      $this->expected = '';
      $this->actual = '';
    }
    return $this->to_response();
  }

  protected function to_response() {
    return [
      'name' => $this->get_name(),
      'success' => $this->is_success(),
      'message' => $this->message,
      'expected' => $this->expected,
      'actual' => $this->actual,
      'reproduce' => $this->reproduce,
    ];
  }

  protected function do_get_request($path, $headers = []) {

    $url = $this->url . $path;
    curl_setopt($this->curl, CURLOPT_URL, $url);
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

    $reproduceStep = array_merge([ "GET $url" ], $headers);
    $this->reproduce[] = $reproduceStep;

    $this->responseBody = curl_exec($this->curl);

    return $this->responseBody;
  }

}
