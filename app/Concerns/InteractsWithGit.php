<?php

namespace App\Concerns;


trait InteractsWithGit
{

    public function isUsingGitFlow(): bool
    {
        // If we have both master and develop branches, we assume we're useing got flow
        $hasDevBranch = strlen($this->runProcess("git branch --list develop", true)) > 0;
        $hasMasterBranch = strlen($this->runProcess("git branch --list master", true)) > 0;

        return ($hasDevBranch && $hasMasterBranch);
    }


    public function getCurrentBranch(): string
    {
        return $this->runProcess('git rev-parse --abbrev-ref HEAD', true);
    }


    public function abortIfThereAreUncommitedFiles(): void
    {
        $hasUncomittedFiles = strlen($this->runProcess("git diff-index HEAD --", true)) > 0;

        if ($hasUncomittedFiles) {
            $this->abort("Aborted: You have uncomitted files.");
        }
    }


    public function abortIfNewCommitsAreAvailableToPull(): void
    {
        // Abort if we do not have a remote repo
        if (strlen($this->runProcess('git remote', true)) === 0) return;

        // Get Branches to check
        $branchesToCheck = ($this->isUsingGitFlow()) ? ['master', 'develop'] : [$this->getCurrentBranch()];
        $outdatedBranches = [];

        // Check if there's something to pull
        foreach ($branchesToCheck as $branch) {
            $this->runProcess("git fetch", true);
            $local = $this->runProcess("git rev-parse {$branch}", true);
            $remote = $this->runProcess("git rev-parse origin/{$branch}", true);
            $base = $this->runProcess("git merge-base {$branch} origin/{$branch}", true);

            if($local !== $remote && $local === $base) $outdatedBranches[] = $branch;

            // $local == $remote Everything up to date
            // $local == $base Need to pull
            // $remote == $base Need to push

        }

        // Abort if there is
        if (count($outdatedBranches)) {
            $this->abort("Aborted: New Commits are available on these branches: " . implode(',', $outdatedBranches));
        }
    }

}
