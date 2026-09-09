<?php

/**
 * Shared helpers for the course-owned grading suite (see grading/README.md).
 *
 * These are plain functions (no autoloading) required by each grading test
 * file so the suite stays runnable via `vendor/bin/pest grading/tests/ModuleN`
 * without touching the learner's composer.json or tests/Pest.php.
 */

/**
 * Run a command from the repo root. Returns [exitCode, combinedOutput].
 *
 * @param  list<string>  $command
 * @param  array<string, string>  $env  Extra environment variables.
 */
function grading_process(array $command, array $env = [], ?string $input = null, int $timeout = 600): array
{
    $process = new Symfony\Component\Process\Process(
        $command,
        base_path(),
        $env === [] ? null : array_merge(getenv(), $env),
        $input,
        $timeout,
    );

    $process->run();

    return [$process->getExitCode(), $process->getOutput().$process->getErrorOutput()];
}

/**
 * The learner repo's base commit. Repos generated from the course template
 * start from a single squashed root commit (the starter snapshot), so the
 * root commit is the starter base for all diff/history assertions.
 */
function grading_base_sha(): string
{
    [$exit, $out] = grading_process(['git', 'rev-list', '--max-parents=0', 'HEAD']);

    if ($exit !== 0 || trim($out) === '') {
        throw new RuntimeException('Could not resolve the repository root commit.');
    }

    $roots = preg_split('/\s+/', trim($out));

    return end($roots);
}

/**
 * First commit that MODIFIES the given path (the root/adding commit does not
 * count). Returns null when the path was never modified after the base.
 */
function grading_first_modifying_commit(string $path): ?string
{
    [$exit, $out] = grading_process(['git', 'log', '--diff-filter=M', '--format=%H', '--reverse', '--', $path]);

    if ($exit !== 0) {
        return null;
    }

    $lines = array_filter(preg_split('/\s+/', trim($out)));

    return $lines === [] ? null : reset($lines);
}

/**
 * First commit in which the given file no longer matches $pattern (i.e. the
 * stub marker disappeared). Returns null if it matches in every revision.
 */
function grading_first_commit_without(string $path, string $pattern): ?string
{
    [$exit, $out] = grading_process(['git', 'log', '--format=%H', '--reverse', '--', $path]);

    if ($exit !== 0) {
        return null;
    }

    foreach (array_filter(preg_split('/\s+/', trim($out))) as $sha) {
        [$showExit, $content] = grading_process(['git', 'show', $sha.':'.$path]);

        if ($showExit === 0 && preg_match($pattern, $content) !== 1) {
            return $sha;
        }
    }

    return null;
}

/** True when $ancestor is an ancestor of $descendant. */
function grading_is_ancestor(string $ancestor, string $descendant): bool
{
    [$exit] = grading_process(['git', 'merge-base', '--is-ancestor', $ancestor, $descendant]);

    return $exit === 0;
}

/** Read a repo file, or '' when it does not exist. */
function grading_file(string $relative): string
{
    $absolute = base_path($relative);

    return is_file($absolute) ? (string) file_get_contents($absolute) : '';
}

/** Response HTML with href/src/action attribute values stripped, so
 *  assertions target human-visible text rather than URLs in attributes. */
function grading_visible_text(string $html): string
{
    return (string) preg_replace('/\b(href|src|action)="[^"]*"/i', '', $html);
}
