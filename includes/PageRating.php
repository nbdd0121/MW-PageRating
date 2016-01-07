<?php
namespace PageRating;

class PageRating {

	public $pageid;
	public $numRating = null;
	public $score = null;

	const MAX_RATING_COUNT = 30;

	public function __construct($pageid) {
		$this->pageid = $pageid;
	}

	private function fetchFromDB() {
		$dbr = wfGetDB(DB_SLAVE);
		$row = $dbr->selectRow('pagerating_records', array(
			'SUM(prr_score) score',
			'COUNT(prr_score) numRating',
		), array(
			'prr_pageid' => $this->pageid,
		), __METHOD__, array(
			'ORDER BY' => 'prr_id DESC',
			'LIMIT' => static::MAX_RATING_COUNT,
		));
		if ($row) {
			$this->numRating = intval($row->numRating);
			$this->score = intval($row->score);
		} else {
			$this->numRating = 0;
			$this->score = 0;
		}
	}

	public function isUserRated(\User $user) {
		if ($user->isAnon()) {
			$userid = $user->getName();
		} else {
			$userid = $user->getId();
		}
		$dbr = wfGetDB(DB_SLAVE);
		$row = $dbr->selectRow('pagerating_records', array('prr_timestamp'), array(
			'prr_pageid' => $this->pageid,
			'prr_user' => $userid,
		));
		if ($row) {
			return $row->prr_timestamp;
		} else {
			return false;
		}
	}

	public function rate(\User $user, $score) {
		if ($user->isAnon()) {
			$userid = $user->getName();
		} else {
			$userid = $user->getId();
		}
		$dbw = wfGetDB(DB_MASTER);
		$dbw->replace('pagerating_records', array('prr_pageid', 'prr_user'), array(
			'prr_user' => $userid,
			'prr_pageid' => $this->pageid,
			'prr_score' => $score,
			'prr_timestamp' => wfTimestamp(TS_MW),
		));
	}

	public function getRating() {
		if ($this->score === null) {
			$this->fetchFromDB();
		}
		if ($this->numRating === 0) {
			return 0;
		}
		return round($this->score / $this->numRating * 10) / 10;
	}

	public function getNumRating() {
		if ($this->score === null) {
			$this->fetchFromDB();
		}
		return $this->numRating;
	}

}