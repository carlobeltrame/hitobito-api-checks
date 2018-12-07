<template>
  <div class="container">
    <div class="section">
      <header class="title">Hitobito API tester</header>
      <form @submit.prevent="runTests">
        <div class="field has-addons">
          <div class="control">
            <input class="input field" type="url" name="hitobitoUrl" v-model="hitobitoUrl" placeholder="Hitobito URL" required />
          </div>
          <div class="control">
            <input class="input field" type="text" name="apiToken" v-model="apiToken" placeholder="API token" />
          </div>
          <div class="control">
            <input class="input field" type="text" name="groupId" v-model="groupId"
                   placeholder="Id of group or layer" required />
          </div>
          <div class="control">
            <button class="button is-info" type="submit">Run {{ testNames.length }} tests</button>
          </div>
        </div>
      </form>
    </div>
    <div v-if="displayTests" class="section">
      <section class="panel">
        <p class="panel-heading">
          Tests run ({{ tests.length }} out of {{ testNames.length }})
        </p>
        <p class="panel-tabs">
          <a class="is-active">all</a>
          <a>failed ({{ numFailedTests }})</a>
          <a>successful ({{ numSuccessfulTests }})</a>
        </p>

        <a class="panel-block" v-for="test in tests" :key="test.name">
          <span v-if="test.success" class="panel-icon has-text-success"><i class="fas fa-check-circle"></i></span>
          <span v-else class="panel-icon has-text-danger"><i class="fas fa-times-circle"></i></span>
          <div>
            <div class="title is-6">{{ test.name }}</div>
            <span v-if="test.success">
              Works as expected.
              <div v-if="test.reproduce"><b>Steps to reproduce:</b>
                <pre class="reproduce" v-for="(step, index) in test.reproduce" :key="index"><div v-for="(line, lineindex) in step" :key="lineindex">{{ line }}</div></pre>
              </div>
            </span>
            <span v-else>
              {{ test.message }}
              <div v-if="test.reproduce"><b>Steps to reproduce:</b>
                <pre class="reproduce" v-for="(step, index) in test.reproduce" :key="index"><div v-for="(line, lineindex) in step" :key="lineindex">{{ line }}</div></pre>
              </div>
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
      tests: [],
      testNames: [],
      hitobitoUrl: '',
      apiToken: '',
      groupId: '',
      displayTests: false
    }
  },
  created() {
    const urlParams = new URLSearchParams(window.location.search)
    this.hitobitoUrl = urlParams.get('hitobitoUrl') || 'https://pbs.puzzle.ch'
    this.apiToken = urlParams.get('apiToken')
    this.groupId = urlParams.get('groupId')
    this.$http.get('tests.php').then(result => {
      this.testNames = result.data
    })
  },
  computed: {
    numSuccessfulTests() {
      return this.tests.filter(test => test.success).length
    },
    numFailedTests() {
      return this.tests.filter(test => !test.success).length
    }
  },
  methods: {
    runTests() {
      const url = new URL(window.location.href)
      this.setQueryParams(url)
      window.history.pushState({hitobitoUrl: this.hitobitoUrl, apiToken: this.apiToken, groupId: this.groupId}, document.title, url.toString());
      this.displayTests = true
      this.tests = []
      this.runSingleTest(0)
    },
    runSingleTest(index) {
      if (index >= this.testNames.length) return;
      let url = new URL(window.location.protocol + '//' + window.location.host + window.location.pathname + 'tests.php');
      url.searchParams.set('test', this.testNames[index])
      this.setQueryParams(url)
      this.$http.get(url.toString()).then(result => {
        this.tests.push(result.data)
        this.runSingleTest(index + 1)
      }).catch(error => {
        this.tests.push({
          name: this.testNames[index],
          success: false,
          message: 'Server side error while running test. ' + error.response.data,
          expected: '',
          actual: '',
          reproduce: ''
        })
        this.runSingleTest(index + 1)
      })
    },
    setQueryParams(url) {
      url.searchParams.set('hitobitoUrl', this.hitobitoUrl)
      url.searchParams.set('apiToken', this.apiToken)
      url.searchParams.set('groupId', this.groupId)
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

  pre.reproduce {
    padding: 5px;
    margin-bottom: 5px;
  }

  .panel-block div {
    width: 100%;
  }
</style>
