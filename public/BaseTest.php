<?php

require_once __DIR__ . '/TestNotApplicableException.php';
require_once __DIR__ . '/AssertionFailedException.php';

abstract class BaseTest {

  protected $url;
  protected $token;
  protected $groupId;
  protected $peoplePermission;
  protected $peopleBelowPermission;
  protected $groupsPermission;
  protected $eventsPermission;
  protected $path;
  protected $curl;
  protected $responseHeaders = [];
  protected $responseBody = null;
  protected $message = '';
  protected $expected = '';
  protected $actual = '';
  protected $reproduce = [];
  protected $success = true;

  public function __construct($url, $token, $tokenGroupId,
                              $peoplePermission, $peopleBelowPermission, $groupsPermission, $eventsPermission,
                              $path) {
    $this->url = $url;
    $this->token = $token;
    $this->groupId = $tokenGroupId;
    $this->peoplePermission = $peoplePermission;
    $this->peopleBelowPermission = $peopleBelowPermission;
    $this->groupsPermission = $groupsPermission;
    $this->eventsPermission = $eventsPermission;
    $this->path = $path;
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
   * This method should also evaluate whether the test is applicable to the given inputs (token, group, permissions,
   * ...) and throw TestNotApplicableException if it isn't.
   *
   * @return void
   * @throws TestNotApplicableException when the test is not applicable to the given input configuration.
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
    } catch (TestNotApplicableException $e) {
      return $this->to_response('not_applicable', [], $e->getMessage());
    } catch (Exception $e) {
      return $this->to_response('fail', $this->reproduce, 'Test setup failed: ' . $e->getMessage());
    }
    // Reset reproduction steps so only the ones in when() are shown
    $this->reproduce = [];
    try {
      $this->when();
    } catch (Exception $e) {
      return $this->to_response('fail', $this->reproduce, 'Test execution failed: ' . $e->getMessage());
    }
    try {
      foreach ($this->then() as $assertion) {
        /** @var $assertion BaseAssertion */
        $assertion($this);
      }
    } catch (AssertionFailedException $e) {
      return $this->to_response('fail', $this->reproduce, $e->getMessage(), $e->get_expected(), $e->get_actual());
    } catch (Exception $e) {
      return $this->to_response('fail', $this->reproduce, 'Assertion failed. ' . $e->getMessage());
    }
    return $this->to_response('success', $this->reproduce);
  }

  protected function to_response($status, $reproduce = [], $message = '', $expected = '', $actual = '') {
    return [
      'name' => $this->get_name(),
      'path' => $this->path,
      'status' => $status,
      'reproduce' => $reproduce,
      'message' => $message,
      'expected' => $expected,
      'actual' => $actual,
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
