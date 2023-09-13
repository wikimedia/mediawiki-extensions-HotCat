<?php
namespace MediaWiki\HotCat\HookHandler;

use Config;
use MediaWiki\Permissions\PermissionManager;
use User;

class BetaFeaturePreferencesHandler {

    private $config;
    private $permissionManager;

    public function __construct(
        Config $config,
        PermissionManager $permissionManager
    ) {
        $this->config = $config;
        $this->permissionManager = $permissionManager;
    }

    public function onGetBetaFeaturePreferences ( $user, &$betaPrefs ) {
        $extensionAssetsPath = $this->config->get( 'ExtensionAssetsPath' );

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