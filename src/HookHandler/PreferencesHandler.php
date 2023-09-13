<?php
namespace MediaWiki\HotCat\HookHandler;

use ExtensionRegistry;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Preferences\Hook\GetPreferencesHook;
use Category;
use MediaWiki\User\UserGroupManager;
use MediaWiki\User\UserIdentity;
use MediaWiki\User\UserOptionsLookup;

class PreferencesHandler implements GetPreferencesHook {

    private $permissionManager;
    private $userOptionsLookup;
    private $userGroupManager;

    public function __construct(
        PermissionManager $permissionManager,
        UserOptionsLookup $userOptionsLookup,
        UserGroupManager $userGroupManager
    ) {
        $this->permissionManager = $permissionManager;
        $this->userOptionsLookup = $userOptionsLookup;
        $this->userGroupManager = $userGroupManager;
    }

    public function onGetPreferences( $user, &$preferences ): void {
        if ( !$this->permissionManager->userHasRight( $user, 'edit' ) ) {
			return;
		}

        $isBetaFeatureLoaded = ExtensionRegistry::getInstance()->isLoaded( 'BetaFeatures' );
        if (
            $isBetaFeatureLoaded && !$this->userOptionsLookup->getOption( $user, 'hotcat-beta-feature-enable' )
        ) {
            return;
        }

        $preferences['hotcat-switch'] = [
            'type' => 'toggle',
            'label-message' => 'tog-hotcat',
            'section' => 'editing/editor'
        ];
    }

    private function isTruthy( $options, $option ): bool {
        return !empty( $options[$option] );
    }

    private function isFalsey( $options, $option ): bool {
        return isset( $options[$option] ) && !$options[$option];
    }

    public function onSaveUserOptions ( $user, &$modifiedOptions, $originalOptions ) {
        $betaFeatureIsEnabled = $this->isTruthy( $originalOptions, 'hotcat-beta-feature-enable' );
        $betaFeatureIsDisabled = !$betaFeatureIsEnabled;

        $betaFeatureWillEnable = $this->isTruthy( $modifiedOptions, 'hotcat-beta-feature-enable' );
        $betaFeatureWillDisable = $this->isFalsey( $modifiedOptions, 'hotcat-beta-feature-enable' );

        $autoEnrollIsEnabled = $this->isTruthy( $originalOptions, 'betafeatures-auto-enroll' );
        $autoEnrollIsDisabled = !$autoEnrollIsEnabled;
        $autoEnrollWillEnable = $this->isTruthy( $modifiedOptions, 'betafeatures-auto-enroll' );

        if (
            ( $betaFeatureIsEnabled && $betaFeatureWillDisable ) ||
            ( $betaFeatureIsDisabled && $betaFeatureWillEnable ) ||
            ( $betaFeatureIsDisabled && $autoEnrollIsDisabled && $autoEnrollWillEnable )
        ) {
            $modifiedOptions[ 'hotcat-switch' ] = false;
        }
    }
}