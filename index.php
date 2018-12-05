<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>hitobito API tester</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.2/css/bulma.css"
        integrity="sha256-dMQYvN6BU9M4mHK94P22cZ4dPGTSGOVP41yVXvXatws=" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/solid.css"
        integrity="sha384-rdyFrfAIC05c5ph7BKz3l5NG5yEottvO/DQ0dCrwD8gzeQDjYBHNr1ucUpQuljos" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/fontawesome.css"
        integrity="sha384-u5J7JghGz0qUrmEsWzBQkfvc8nK3fUT7DCaQzNQ+q4oEXhGSx+P2OqjWsfIRB8QT" crossorigin="anonymous">
  <style>
    @import url('https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,600,300italic');

    body {
      font-family: "Source Sans Pro", BlinkMacSystemFont, -apple-system, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", "Helvetica", "Arial", sans-serif;
    }
  </style>
</head>
<body>
<?php
function getParam($paramName) {
  return filter_input(INPUT_GET, $paramName) ?: '';
};

function getClassesFromDirectory($directory) {
  $predeclaredClasses = get_declared_classes();
  $files = new FileSystemIterator($directory, FileSystemIterator::SKIP_DOTS);
  foreach ($files as $file) {
    require_once $file->getPathname();
  }
  return array_diff(get_declared_classes(), $predeclaredClasses);
};

$curl = curl_init();

$hitobitoUrl = getParam('hitobitoUrl');
$apiToken = getParam('apiToken');
$groupId = getParam('groupId');
?>
<div class="container">
  <div class="section">
    <header class="title">Hitobito API tester</header>
    <form method="GET">
      <div class="field has-addons">
        <div class="control">
          <input class="input field" type="url" name="hitobitoUrl"
                 value="<?= $hitobitoUrl ?: 'https://pbs.puzzle.ch' ?>" required />
        </div>
        <div class="control">
          <input class="input field" type="text" name="apiToken" value="<?=$apiToken?>" placeholder="API token" <?= $apiToken === '' ? 'autofocus ' : '' ?>required />
        </div>
        <div class="control">
          <input class="input field" type="text" name="groupId" value="<?=$groupId?>" placeholder="Id of group or layer" required />
        </div>
        <div class="control">
          <button class="button is-info" type="submit">Test API</button>
        </div>
      </div>
    </form>
  </div>
  <?php if ($hitobitoUrl && $apiToken && $groupId) : ?>
    <div class="section">
      <section class="panel">
        <p class="panel-heading">
          Tests run
        </p>
        <p class="panel-tabs">
          <a class="is-active">all</a>
          <a>failed</a>
          <a>successful</a>
        </p>
        <?php
        foreach (getClassesFromDirectory('tests') as $testClass) {
          if (is_subclass_of($testClass, 'BaseTest')) {
            /** @var BaseTest $testInstance */
            $testInstance = new $testClass($hitobitoUrl, $apiToken, $groupId, $curl);
            $testInstance->evaluate();
            $testInstance->render();
          }
        }
        ?>
      </section>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
