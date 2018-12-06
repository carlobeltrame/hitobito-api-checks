<template>
  <div class="container">
    <div class="section">
      <header class="title">Hitobito API tester</header>
      <div class="field has-addons">
        <div class="control">
          <input class="input field" type="url" name="hitobitoUrl" v-model="hitobitoUrl" required />
        </div>
        <div class="control">
          <input class="input field" type="text" name="apiToken" v-model="apiToken"
                 placeholder="API token" :autofocus="apiToken === ''" required />
        </div>
        <div class="control">
          <input class="input field" type="text" name="groupId" v-model="groupId"
                 placeholder="Id of group or layer" required />
        </div>
        <div class="control">
          <button class="button is-info" type="submit">Test API</button>
        </div>
      </div>
    </div>
    <div v-if="displayTests" class="section">
      <section class="panel">
        <p class="panel-heading">
          Tests run
        </p>
        <p class="panel-tabs">
          <a class="is-active">all</a>
          <a>failed</a>
          <a>successful</a>
        </p>

        <a class="panel-block" v-for="test in tests" :key="test.name">
          <span v-if="test.success" class="panel-icon has-text-success"><i class="fas fa-check-circle"></i></span>
          <span v-else class="panel-icon has-text-danger"><i class="fas fa-times-circle"></i></span>
          <div>
            <div class="title is-6">{{ test.name }}</div>
            <span v-if="test.success">Works as expected.</span>
            <span v-else>
              {{ test.message }}
              <div v-if="test.expected"><b>Expected:</b> {{ test.expected }}</div>
              <div v-if="test.actual"><b>Actual:</b> {{ test.actual }}</div>
            </span>
          </div>
        </a>
      </section>
    </div>
  </div>
</template>

<script>
export default {
  name: 'app',
  data() {
    return {
      tests: [{ name: 'Dummy test', success: true, message: '' }],
      hitobitoUrl: 'https://pbs.puzzle.ch',
      apiToken: '',
      groupId: '',
      displayTests: true
    }
  }
}
</script>

<style>
  @import url('https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,600,300italic');

  body, button, input, select, textarea {
    font-family: "Source Sans Pro", BlinkMacSystemFont, -apple-system, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", "Helvetica", "Arial", sans-serif;
  }

  .panel-block div .title.is-6 {
    margin-bottom: 5px;
  }
</style>
