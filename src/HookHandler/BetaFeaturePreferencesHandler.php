<?php
namespace MediaWiki\HotCat\HookHandler;

use MediaWiki\Config\Config;
use MediaWiki\MainConfigNames;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\User\User;

class BetaFeaturePreferencesHandler {

	public function __construct(
		private readonly Config $config,
		private readonly PermissionManager $permissionManager,
	) {
	}

	/**
	 * @param User $user
	 * @param array[] &$betaPrefs
	 */
	public function onGetBetaFeaturePreferences( $user, &$betaPrefs ) {
		$extensionAssetsPath = $this->config->get( MainConfigNames::ExtensionAssetsPath );

		if (
			$this->permissionManager->userHasRight( $user, 'edit' )
		) {
			$url = "https://mediawiki.org/wiki/";
			$infoLink = $url . "Extension:HotCat";
			$discussionLink = $url . "Extension_talk:HotCat";

			$betaPrefs['hotcat-beta-feature-enable'] = [
				'label-message' => 'hotcat-title',
				'desc-message' => 'hotcat-desc',
				'info-link' => $infoLink,
				'discussion-link' => $discussionLink,
				'requirements' => [
					'javascript' => true,
				],
			];
		}
	}
}
