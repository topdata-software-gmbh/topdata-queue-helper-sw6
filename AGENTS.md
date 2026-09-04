# AGENTS.md

## What this repo is
A Shopware 6 plugin (`type: shopware-platform-plugin`, composer name `topdata/queue-helper`, v1.1.0) that exposes experimental `bin/console` commands for debugging queues, scheduled tasks, and product-export sales channels. All commands modify or read the Shopware DB directly via `Doctrine\DBAL\Connection` — they do **not** use the DAL / repositories.

PSR-4 namespace `Topdata\TopdataQueueHelperSW6\` → `src/`. Plugin entry class: `src/TopdataQueueHelperSW6.php` (bare `extends Plugin`, no overrides).

## Layout
- `src/Command/` — one Symfony Console `Command` per CLI. Command names are set via `protected static $defaultName`.
- `src/Service/` — autowired business logic. `QueueService`, `ScheduledTaskService`, `ExportHelperService`, `DatabaseHelperService`.
- `src/Helper/CliStyle.php` — thin wrapper around `SymfonyStyle`; used by every command.
- `src/Util/` — small static helpers (`UtilDate`, `UtilDict`, `UtilCliTable`, `UtilTextTable`, `UtilDebug`, `AnsiColor`).
- `src/Resources/config/services.xml` — DI registration. New commands **must** be added here as `<service id="..." autowire="true"><tag name="console.command"/></service>`, otherwise Shopware will not see them.

## Installed commands (registered in services.xml)
- `topdata:queue-helper:debug-queue`
- `topdata:queue-helper:list-zombies`
- `topdata:queue-helper:reset-zombies` (asks for confirmation; updates `scheduled_task.status` from `running` → `scheduled` for detected zombies)
- `topdata:queue-helper:enqueue:list` (options: `--search/-s`, `--count-only/-c`)
- `topdata:queue-helper:export:list` (options: `--search/-s`, `--all/-a`)
- `topdata:queue-helper:reset-queue` (truncates `enqueue`; sets `scheduled_task.status` `queued` → `scheduled`)
- `topdata:queue-helper:scheduled-task:list` (option: `--search/-s`)

The Changelog mentions `topdata:queue-helper:delete-zombies` but no such command exists — the actual command is `reset-zombies`. Update Changelog if you rename commands.

## Install / update (run from the host Shopware project, not from this repo)
```
bin/console plugin:refresh
bin/console plugin:install TopdataQueueHelperSW6 -ac    # first install
bin/console plugin:update  TopdataQueueHelperSW6 -c    # subsequent updates
bin/console plugin:list                                 # verify
```
This repo has **no** standalone build, test, lint, or typecheck step. There are no `vendor/`, CI, pre-commit hooks, or test suites — `composer.json` only declares the autoload.

## Code conventions / gotchas
- All files use `<?php declare(strict_types=1);` and the `Topdata\TopdataQueueHelperSW6\` namespace.
- New commands: extend `Symfony\Component\Console\Command\Command`, accept collaborators via constructor, set `$defaultName` + `$defaultDescription`, and instantiate `CliStyle($input, $output)` in `execute()`. Wire in `services.xml`.
- SQL is written by hand against `Connection` (`executeQuery` / `executeStatement`). BINARY UUIDs are returned/stored as hex via `LOWER(HEX(id))` and passed back with `hex2bin` (see `ScheduledTaskService::deleteZombies`).
- `ScheduledTaskService::_isZombie()` (`src/Service/ScheduledTaskService.php:61`) is unfinished: it always returns `true` after a `dump(...)` call. Do not rely on it; `findZombies()` uses an inline SQL heuristic (certainty score) instead and is the real entrypoint.
- `ScheduledTaskService::findZombies()` uses `UTC_TIMESTAMP()` and treats `run_interval` as seconds.
- `DatabaseHelperService::fetchRows()` reflects over table columns and excludes the given list; works only on real Shopware tables (`message_queue_stats`, `enqueue`, `scheduled_task`).
- Commands are marked experimental in `README.md` and several have no input validation — they `DELETE FROM enqueue` / `UPDATE scheduled_task` against the live DB. Do not run on production without a backup.

## Things that are NOT in this repo
- No `tests/` directory, no PHPUnit config, no CI workflows under `.github/`.
- No `.gitignore` (none needed — there is no build output).
- No `opencode.json` / `opencode.jsonc` / `.cursorrules` / `CLAUDE.md`.
- No migrations — the plugin reads/writes tables that Shopware core creates.