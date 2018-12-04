<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>title</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.2/css/bulma.css" integrity="sha256-dMQYvN6BU9M4mHK94P22cZ4dPGTSGOVP41yVXvXatws=" crossorigin="anonymous" />
  <style>
    @import url('https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,600,300italic');
    body {
      font-family: "Source Sans Pro", BlinkMacSystemFont, -apple-system, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", "Helvetica", "Arial", sans-serif;
      padding: 20px;
    }
  </style>
</head>
<body>
<?php
$getParam = function ($paramName) {
  return filter_input(INPUT_GET, $paramName) ?: '';
};

$hitobitoUrl = $getParam('hitobitoUrl');
$apiToken = $getParam('apiToken');
$groupId = $getParam('groupId');
?>
<header class="title">Hitobito API tester</header>
<form method="GET">
  <div class="field has-addons">
    <div class="control">
      <input class="input field" type="url" name="hitobitoUrl" value="<?=$hitobitoUrl ?: 'https://pbs.puzzle.ch'?>" />
    </div>
    <div class="control">
      <input class="input field" type="text" name="apiToken" placeholder="API token" autofocus />
    </div>
    <div class="control">
      <input class="input field" type="text" name="groupId" placeholder="Id of group or layer" />
    </div>
    <div class="control">
      <button class="button is-info" type="submit" name="submit">Test API</button>
    </div>
  </div>
</form>
<?php
?>
</body>
</html>
