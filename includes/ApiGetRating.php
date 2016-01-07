<?php
namespace PageRating;
class ApiGetRating extends \ApiBase {

	public function execute() {
		$params = $this->extractRequestParams();
		$pageid = $params['pageid'];

		$user = $this->getUser();

		$title = \Title::newFromId($pageid);
		if (!$title) {
			$this->dieUsageMsg(array('nosuchpageid', $pageid));
		}

		$rater = new PageRating($pageid);

		$data = array();
		$timeRated = $rater->isUserRated($user);
		if ($timeRated) {
			$data['rated'] = $timeRated;
		}
		$data['numrating'] = $rater->getNumRating();
		$data['averagescore'] = $rater->getRating();

		$this->getResult()->addValue(null, $this->getModuleName(), $data);

		return true;
	}

	protected function getAllowedParams() {
		return array(
			'pageid' => array(
				\ApiBase::PARAM_TYPE => 'integer',
				\ApiBase::PARAM_REQUIRED => true,
			));
	}

	protected function getExamplesMessages() {
		return array(
			'action=getrating&pageid=1' => 'apihelp-getrating-example-1',
		);
	}
}
