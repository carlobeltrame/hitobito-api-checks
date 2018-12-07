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
            <button class="button is-info" type="submit">Run tests</button>
          </div>
        </div>
      </form>
    </div>
    <div class="section">
      <section class="panel">
        <p class="panel-heading">
          Tests run ({{ numCompletedTests }} out of {{ numTests }})
        </p>
        <p class="panel-tabs">
          <a :class="statusFilter === '' ? 'is-active' : ''" @click="filterByStatus('')">all ({{ numTests }})</a>
          <a :class="statusFilter === 'not_run' ? 'is-active' : ''" @click="filterByStatus('not_run')">not run ({{ numNotRunTests }})</a>
          <a :class="statusFilter === 'success' ? 'is-active' : ''" @click="filterByStatus('success')">successful ({{ numSuccessfulTests }})</a>
          <a :class="statusFilter === 'fail' ? 'is-active' : ''" @click="filterByStatus('fail')">failed ({{ numFailedTests }})</a>
          <a :class="statusFilter === 'not_applicable' ? 'is-active' : ''" @click="filterByStatus('not_applicable')">not applicable ({{ numNotApplicableTests }})</a>
        </p>

        <a class="panel-block" v-for="test in filteredTests" :key="test.name">
          <span v-if="test.status === 'success'" class="panel-icon has-text-success"><i class="fas fa-check-circle"></i></span>
          <span v-else-if="test.status === 'fail'" class="panel-icon has-text-danger"><i class="fas fa-times-circle"></i></span>
          <span v-else-if="test.status === 'not_applicable'" class="panel-icon has-text-grey-light"><i class="fas fa-ban"></i></span>
          <span v-else-if="test.status === 'not_run'" class="panel-icon"></span>
          <span v-else-if="test.status === 'running'" class="panel-icon has-text-info"><i class="fas fa-spinner fa-spin"></i></span>
          <div>
            <div class="title is-6">{{ test.name }}</div>
            <span v-if="test.status === 'success'">
              Works as expected.
              <div v-if="test.reproduce && test.reproduce.length"><b>Steps to reproduce:</b>
                <pre class="reproduce" v-for="(step, index) in test.reproduce" :key="index"><div v-for="(line, lineindex) in step" :key="lineindex">{{ line }}</div></pre>
              </div>
            </span>
            <span v-else-if="test.status === 'fail'">
              {{ test.message }}
              <div v-if="test.reproduce && test.reproduce.length"><b>Steps to reproduce:</b>
                <pre class="reproduce" v-for="(step, index) in test.reproduce" :key="index"><div v-for="(line, lineindex) in step" :key="lineindex">{{ line }}</div></pre>
              </div>
              <div v-if="test.expected"><b>Expected:</b> {{ test.expected }}</div>
              <div v-if="test.actual"><b>Actual:</b> {{ test.actual }}</div>
            </span>
            <span v-else-if="test.status === 'not_applicable'">
              Test is not applicable to the given input parameters.
              <div v-if="test.message"><b>Reason:</b> {{ test.message }}</div>
            </span>
            <span v-else-if="test.status === 'not_run'">
              Not yet run.
            </span>
            <span v-else-if="test.status === 'running'">
              Running...
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
      hitobitoUrl: '',
      apiToken: '',
      groupId: '',
      statusFilter: '',
    }
  },
  created() {
    const urlParams = new URLSearchParams(window.location.search)
    this.hitobitoUrl = urlParams.get('hitobitoUrl') || 'https://pbs.puzzle.ch'
    this.apiToken = urlParams.get('apiToken')
    this.groupId = urlParams.get('groupId')
    this.$http.get('tests.php').then(result => {
      this.tests = result.data.map(testPath => ({ name: testPath.split('/').pop(), path: testPath, status: 'not_run' }))
    })
  },
  computed: {
    numTests() {
      return this.tests.length
    },
    numCompletedTests() {
      return this.tests.filter(test => test.status !== 'not_run').length
    },
    numSuccessfulTests() {
      return this.tests.filter(test => test.status === 'success').length
    },
    numFailedTests() {
      return this.tests.filter(test => test.status === 'fail').length
    },
    numNotRunTests() {
      return this.tests.filter(test => test.status === 'not_run').length
    },
    numNotApplicableTests() {
      return this.tests.filter(test => test.status === 'not_applicable').length
    },
    filteredTests() {
      return this.tests.filter(test => test.status === this.statusFilter || this.statusFilter === '')
    }
  },
  methods: {
    runTests() {
      const url = new URL(window.location.href)
      this.setQueryParams(url)
      window.history.pushState({hitobitoUrl: this.hitobitoUrl, apiToken: this.apiToken, groupId: this.groupId}, document.title, url.toString());
      this.tests = this.tests.map( test => ({ ...test, status: 'not_run' }))
      this.runSingleTest(0)
    },
    runSingleTest(index) {
      if (index >= this.numTests) return;
      let singleTest = this.tests[index]
      this.$set(this.tests, index, { ...singleTest, status: 'running' })
      let url = new URL(window.location.protocol + '//' + window.location.host + window.location.pathname + 'tests.php');
      url.searchParams.set('test', singleTest.path)
      this.setQueryParams(url)
      this.$http.get(url.toString()).then(result => {
        this.$set(this.tests, index, result.data)
        this.runSingleTest(index + 1)
      }).catch(error => {
        this.$set(this.tests, index, {
          name: singleTest.name,
          path: singleTest.path,
          status: 'fail',
          message: 'Server side error while running test. ' + error.response.data,
          expected: '',
          actual: '',
          reproduce: []
        })
        this.runSingleTest(index + 1)
      })
    },
    setQueryParams(url) {
      url.searchParams.set('hitobitoUrl', this.hitobitoUrl)
      url.searchParams.set('apiToken', this.apiToken)
      url.searchParams.set('groupId', this.groupId)
    },
    filterByStatus(status) {
      this.statusFilter = status
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
