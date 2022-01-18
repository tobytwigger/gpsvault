---
layout: docs
title: Tasks
nav_order: 9
has_children: true
---

# Tasks

{: .no_toc }

<details open markdown="block">
  <summary>
    Contents
  </summary>
  {: .text-delta }
1. TOC
{:toc}
</details>

---

## Create a task

Extend `App\Services\Sync\Task`.

- description. A description as to what the task does.
- Name. A name for the task.
- run: The function that actually does the task running
  - $this->user() gets the current user
- validationRules: An array of rules that validate config
- requiredConfig: config that is needed for it to work
- fail: Call with a message to fail the task
- Succeed: stop the task and mark it as succeeded with a message
- offerBail: Call this as often as possible with a message. If a user cancels the task, it will cancel at this point
- line: Add a message
- percentage: Update the percentage through the task we are
- config: Get a config key with a default

Register with `App\Services\Sync\Task::registerTask(TaskClass::class)`.

## Start a sync

- Call `$sync = App\Services\Sync\Sync::start()`.
- Add tasks with `$symc->withTask($task, $config)`. Task is an instanciated class, and config is an array of config to use on the task.
- When ready, run `$sync->dispatch()` to start the sync.

`$sync->cancel()` - Cancel the sync
`$sync->pendingTasks()` - Get relationship to pending tasks
`$sync->tasks()` - Get relationship to all tasks
