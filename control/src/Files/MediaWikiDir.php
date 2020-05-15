<?php

namespace Addshore\Mwdd\Files;

class MediaWikiDir {

	private $dir;

	/**
	 * Passed through realpath on the host
	 */
	private $realPath;

	public function __construct( string $dir) {
		$this->dir = $dir;
	}

	public function ensurePresent() {
		// Ignore errors......
		// Maybe we want to show errors though?
		//@mkdir($this->dir, 0777, true);
		passthru('mkdir -p ' . $this->dir);
	}

	private function getRealPath() {
		if(!$this->realPath) {
			$this->ensurePresent();
			$dir = trim(shell_exec('realpath ' . $this->dir));
			$this->realPath = $dir;
		}
		return $this->realPath;
	}

	public function hasDotGitDirectory() {
		// Bash foo from https://stackoverflow.com/a/47677632/4746236
		// Use bash instead of PHP as at least on Windows ~/dev/git/gerrit/mediawiki/core//.git returns false with file_exists when it really does
		// TODO move to LS shell file?
		return (int)trim(shell_exec('(ls ' . $this->getRealPath().'/.git' . ' >> /dev/null 2>&1 && echo 1) || echo 0'));
	}

	public function __toString() {
		return $this->getRealPath();
	}

}
