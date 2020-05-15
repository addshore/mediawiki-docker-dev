<?php

namespace Addshore\Mwdd\Files;

class MediaWikiDir {

	private $dir;

	public function __construct( string $dir) {
		$this->dir = $dir;
	}

	public function ensurePresent() {
		// Ignore errors......
		// Maybe we want to show errors though?
		// Maybe this should be done in bash to mean paths always work nicely? It will need to be done there for the dc setup
		@mkdir($this->dir, 0777, true);
	}

	public function hasDotGitDirectory() {
		// Bash foo from https://stackoverflow.com/a/47677632/4746236
		// Use bash instead of PHP as at least on Windows ~/dev/git/gerrit/mediawiki/core//.git returns false with file_exists when it really does
		// TODO move to LS shell file?
		return (int)trim(shell_exec('(ls ' . $this->dir.'/.git' . ' >> /dev/null 2>&1 && echo 1) || echo 0'));
	}

	public function __toString() {
		return $this->dir;
	}

}
