<?php

namespace MediaWiki\HotCat\HookHandler;

use ExtensionRegistry;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\MediaWikiServices;
use Category;
use MediaWiki\User\UserOptionsLookup;

class PageDisplayHandler implements BeforePageDisplayHook {

    /** @var PermissionManager */
    private $permissionManager;
    /** @var UserOptionsLookup */
    private $userOptionsLookup;

    /**
     * @param PermissionManager $permissionManager
     * @param UserOptionsLookup $userOptionsLookup
     */
    public function __construct(
        PermissionManager $permissionManager,
        UserOptionsLookup $userOptionsLookup
    ) {
        $this->permissionManager = $permissionManager;
        $this->userOptionsLookup = $userOptionsLookup;
    }

    /**
     * @inheritDoc
     */
    public function onBeforePageDisplay($out, $skin): void
    {
        $services = MediaWikiServices::getInstance();
        $extensionRegistry = ExtensionRegistry::getInstance();

        $user = $out->getUser();
        $isBetaFeatureLoaded = $extensionRegistry->isLoaded( 'BetaFeatures' );

        if (
            !$this->permissionManager->userHasRight($user, 'edit' ) ||
            !$this->userOptionsLookup->getOption( $user, 'hotcat-switch' ) ||
            ( $isBetaFeatureLoaded &&
                !$this->userOptionsLookup->getOption( $user, 'hotcat-beta-feature-enable' )
            )
        ) {
            return;
        }

        $out->addModules( 'ext.hotcat' );
    }
}