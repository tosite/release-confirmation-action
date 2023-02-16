# release-confirmation-action

These GitHub Actions are intended to prevent release leaks after a merge.

## The mechanism is:

1. **after merging(A)**
    - Assign the label `action-merged`
2. **after release(B)**
    - If the release flow is built with Github Actions:
        - remove `action-merged` and add `action-released` from this actions
    - If the release flow is not built with GitHub Actions:
        - manually remove `action-merged` and add `action-released`
3. **notify(C)**
    - detect and notify pull requests with `action-merged` still attached

## About each setting:

### after merging(A)

```yml
on:
  pull_request:
    # only "closed" events.
    branches:
      - main
    types: [closed]

jobs:
  after-merged:
    runs-on: ubuntu-latest
    # only merged.
    if: github.event.pull_request.merged == true
    steps:
      -
        name: Add merged label
        uses: tosite/release-confirmation-action@v0
        with:
          action: merged
```

### after release(B):

```yml
jobs:
  deployment:
    # your deployment workflow
  after-released:
    runs-on: ubuntu-latest
    steps:
      -
        name: Remove merged label and Add released label
        uses: tosite/release-confirmation-action@v0
        with:
          action: released
```

### notify(C)

```yml
on:
  schedule:
  - cron: 0 10 * * *

jobs:
   release-comment:
      runs-on: ubuntu-latest
      steps:
         -
            name: Notify unreleased pull requests
            uses: tosite/release-confirmation-action@v0
            with:
               action: notify
               channel: '#random'
               showReleasedPulls: 1
               TargetRepositories: |
                  your/repoA
                  your/repoB
               releasedDays: 5
               # Please save your Slack Webhook url to your GitHub secret.
               slackWebhook: ${{ secrets.SLACK_WEBHOOK }}
```

## setting

### common action

|key| values                         | required          |description|
| --- |--------------------------------|-------------------| --- |
| action | `merged, released, notify`     | ⭕ | Specify actions. |
| mergedLabel | `action-merged`                | optional          | After merged label. |
| releasedLabel | `action-released`              | optional          | After released label. |
| isDebug | `on=1,off=0`                   | optional          | Specify debug mode. |
| baseUrl | `https://api.github.com/repos` | optional          | GitHub API URL. |

### nofify action

|key| values                                             |required|description|
| --- |----------------------------------------------------| --- | -- |
| slackWebhook | `https://hooks.slack.com/services/xxxx/xxxx/xxxxx` | ⭕ | https://slack.com/services/new/incoming-webhook |
| notifySubject | `string`                                           | optional | Title to notify Slack. |
| TargetRepositories | `"your/repoA\nyour/repoB"`                           | optional | These are the repositories that will be monitored.(default: repository where the action is running) |
| unreleasedPullsSubject | `string`                                           | optional | Title to unreleased. |
| mention | `<@USER_ID>, <!here>, <!channel>`                  | optional | Specify the user to mentions. |
| showReleasedPulls | `on=1, off=0`                                      | optional | Whether to be notified of released pull requests. |
| releasedPullsSubject | `string`                                           | optional | Title to released(To enable, set showReleasedPulls to 1). |
| releasedDays | `5`                                                | optional | How many days prior to the release do you want to pull a released pull request?(To enable, set showReleasedPulls to 1) |
