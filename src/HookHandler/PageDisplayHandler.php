<?php

namespace MediaWiki\HotCat\HookHandler;

use ExtensionRegistry;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\MediaWikiServices;
use Category;
use MediaWiki\User\UserOptionsLookup;

class PageDisplayHandler implements BeforePageDisplayHook {

    private $permissionManager;
    private $userOptionsLookup;

    public function __construct(
        PermissionManager $permissionManager,
        UserOptionsLookup $userOptionsLookup
    ) {
        $this->permissionManager = $permissionManager;
        $this->userOptionsLookup = $userOptionsLookup;
    }

    public function onBeforePageDisplay($out, $skin): void
    {
        $services = MediaWikiServices::getInstance();
        $extensionRegistry = ExtensionRegistry::getInstance();

        $user = $out->getUser();
        $isBetaFeatureLoaded = $extensionRegistry->isLoaded( 'BetaFeatures' );

        if (
            !$this->permissionManager->userHasRight($user, 'edit' )
        ) {
            return;
        }

        $out->addModules( 'ext.hotcat' );
    }
}