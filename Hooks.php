<?php
namespace PageRating;
class Hooks {
	public static function onBeforePageDisplay(\OutputPage &$output, \Skin &$skin) {
		$title = $output->getTitle();

		// Disallow commenting on pages without article id
		if ($title->getArticleID() == 0) {
			return true;
		}

		if ($title->isSpecialPage()) {
			return true;
		}

		// These could be explicitly allowed in later version
		if (!$title->canTalk()) {
			return true;
		}

		if ($title->isTalkPage()) {
			return true;
		}

		if ($title->isMainPage()) {
			return true;
		}

		// Do not display when printing
		if ($output->isPrintable()) {
			return true;
		}

		// Disable if not viewing
		if ($skin->getRequest()->getVal('action', 'view') != 'view') {
			return true;
		}

		// Blacklist several namespace
		if (in_array($title->getNamespace(), array(
			NS_MEDIAWIKI,
			NS_TEMPLATE,
			NS_CATEGORY,
			NS_FILE,
			NS_USER,
		))) {
			return true;
		}

		$output->addModules('ext.pagerating');
		return true;
	}

	public static function onLoadExtensionSchemaUpdates(\DatabaseUpdater $updater) {
		$updater->addExtensionTable('pagerating_records', __DIR__ . '/sql/create-table.sql', true);
		return true;
	}
}