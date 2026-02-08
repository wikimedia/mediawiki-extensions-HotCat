<?php

namespace MediaWiki\HotCat\Test\Integration\HookHandler;

use MediaWiki\HotCat\HookHandler\PageDisplayHandler;
use MediaWiki\Output\OutputPage;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Title\Title;
use MediaWiki\User\Options\UserOptionsLookup;
use MediaWiki\User\User;
use MediaWikiIntegrationTestCase;

/**
 * @group HotCat
 * @covers \MediaWiki\HotCat\HookHandler\PageDisplayHandler
 */
class PageDisplayHandlerTest extends MediaWikiIntegrationTestCase {

	private function getPermissionManager(): PermissionManager {
		$permissionManager = $this->createMock( PermissionManager::class );
		$permissionManager->method( 'userHasRight' )->willReturn( true );
		return $permissionManager;
	}

	private function getUserOptionsLookup(): UserOptionsLookup {
		$userOptionsLookup = $this->createMock( UserOptionsLookup::class );
		$userOptionsLookup->method( 'getOption' )->willReturn( true );
		return $userOptionsLookup;
	}

	private function getPageDisplayHandler( array $overrides = [] ): PageDisplayHandler {
		return new PageDisplayHandler(
			$overrides[ 'PermissionManager' ] ?? $this->getPermissionManager(),
			$overrides[ 'UserOptionsLookup' ] ?? $this->getUserOptionsLookup()
		);
	}

	private function getOutputPage( array $overrides = [] ): OutputPage {
		$out = $this->getMockBuilder( OutputPage::class )
			->disableOriginalConstructor()
			->setMethodsExcept( [
				'addModules',
				'getModules',
			] )
			->getMock();
		$out->method( 'getUser' )->willReturn( $overrides['user'] ?? $this->createMock( User::class ) );
		$out->method( 'getTitle' )->willReturn( $overrides['title'] ?? $this->createMock( Title::class ) );
		return $out;
	}
}
