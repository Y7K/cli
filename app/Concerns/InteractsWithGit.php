<?php

namespace App\Concerns;


trait InteractsWithGit
{

    public function isUsingGitFlow(): bool
    {
        // If we have both master and develop branches, we assume we're useing got flow
        $hasDevBranch = strlen($this->runProcess("git branch --list develop")) > 0;
        $hasMasterBranch = strlen($this->runProcess("git branch --list master")) > 0;

        return ($hasDevBranch && $hasMasterBranch);
    }


    public function getCurrentBranch(): string
    {
        return $this->runProcess('git rev-parse --abbrev-ref HEAD');
    }

    public function abortIfThereAreUncommitedFiles(): void
    {
        $hasUncomittedFiles = strlen($this->runProcess("git diff-index HEAD --")) > 0;

        if ($hasUncomittedFiles) {
            $this->abort("Aborted: You have uncomitted files.");
        }
    }

    public function abortIfNewCommitsAreAvailableToPull(): void
    {
        // Get Branches to check
        $branchesToCheck = ($this->isUsingGitFlow()) ? ['master', 'develop'] : [$this->getCurrentBranch()];
        $outdatedBranches = [];

        if (strlen($this->runProcess('git remote')) === 0) return;

        // Check if there's something to pull
        foreach ($branchesToCheck as $branch) {
            $local = $this->runProcess("git rev-parse {$branch}");
            $remote = $this->runProcess("git rev-parse origin/{$branch}");
            if ($local !== $remote) $outdatedBranches[] = $branch;
        }

        // Abort if there is
        if (count($outdatedBranches)) {
            $this->abort("Aborted: New Commits are available on these branches: " . implode(',', $outdatedBranches));
        }
    }

}
