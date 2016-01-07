<?php
namespace PageRating;
class ApiRate extends \ApiBase {

	public function execute() {
		$params = $this->extractRequestParams();
		$pageid = $params['pageid'];

		$user = $this->getUser();
		$this->dieOnBadUser($user);

		$title = \Title::newFromId($pageid);
		if (!$title) {
			$this->dieUsageMsg(array('nosuchpageid', $pageid));
		}

		$rater = new PageRating($pageid);
		$rater->rate($user, $params['score']);

		$data = array();
		$data['myscore'] = $params['score'];
		$data['rated'] = wfTimestamp(TS_MW);
		$data['numrating'] = $rater->getNumRating();
		$data['averagescore'] = $rater->getRating();

		$this->getResult()->addValue(null, $this->getModuleName(), $data);
		return true;
	}

	protected function dieOnBadUser(\User $user) {
		if ($user->isBlocked()) {
			$this->dieUsageMsg(array('blockedtext'));
		}
	}

	protected function getAllowedParams() {
		return array(
			'pageid' => array(
				\ApiBase::PARAM_TYPE => 'integer',
				\ApiBase::PARAM_REQUIRED => true,
			),
			'score' => array(
				\ApiBase::PARAM_TYPE => 'integer',
				\ApiBase::PARAM_REQUIRED => true,
			));
	}

	protected function getExamplesMessages() {
		return array(
			'action=rate&pageid=1&score=5' => 'apihelp-rate-example-1',
		);
	}
}
