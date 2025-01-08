<?php

namespace MediaWiki\HotCat\Test\Unit\HookHandler;

use MediaWiki\HotCat\HookHandler\PreferencesHandler;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\User\UserGroupManager;
use MediaWiki\User\UserIdentity;
use MediaWiki\User\UserOptionsLookup;
use MediaWikiIntegrationTestCase;
use User;

/**
 * @group HotCat
 * @covers \MediaWiki\HotCat\HookHandler\PreferencesHandler
 */
class PreferencesHandlerTest extends MediaWikiIntegrationTestCase {

	private function getPreferencesHandler( array $options = [] ): PreferencesHandler {
		return new PreferencesHandler( ...array_values( array_merge(
			[
				'permissionManager' => $this->createMock( PermissionManager::class ),
				'userOptionsLookup' => $this->createMock( UserOptionsLookup::class ),
				'userGroupManager' => $this->createMock( UserGroupManager::class ),
			],
			$options
		) ) );
	}

	/**
	 * @dataProvider provideOnSaveUserOptionsRestoreDefaultPreferences
	 */
	public function testOnSaveUserOptionsRestoreDefaultPreferences( $originalOptions, $modifiedOptions ) {
		$user = $this->createMock( UserIdentity::class );

		$handler = $this->getPreferencesHandler( [] );
		$handler->onSaveUserOptions( $user, $modifiedOptions, $originalOptions );

		$this->assertFalse( $modifiedOptions[ 'hotcat-switch' ] );
	}

	public static function provideOnSaveUserOptionsRestoreDefaultPreferences() {
		return [
			'Disable beta feature' => [
				[
					'hotcat-beta-feature-enable' => true
				],
				[
					'hotcat-beta-feature-enable' => false
				],
			],
			'Enable beta feature' => [
				[
					'hotcat-beta-feature-enable' => false
				],
				[
					'hotcat-beta-feature-enable' => true
				],
			],
			'Enable auto enroll' => [
				[
					'hotcat-beta-feature-enable' => false,
					'betafeatures-auto-enroll' => false
				],
				[
					'betafeatures-auto-enroll' => true
				],
			],
		];
	}

	public function testOnGetPreferences() {
		$user = $this->createMock( User::class );

		$permissionManager = $this->createMock( PermissionManager::class );
		$permissionManager->method( 'userHasRight' )->willReturn( true );

		$handler = $this->getPreferencesHandler( [
			'permissionManager' => $permissionManager
		] );

		$preferences = [];
		$handler->onGetPreferences( $user, $preferences );
		$this->assertArrayHasKey( 'hotcat-switch', $preferences );
	}
}
