<?php

namespace MediaWiki\Extension\HotCat;

use MediaWiki\Hook\GetPreferencesHook;

class Hooks implements GetPreferencesHook {
    public static function onGetPreferences( $user, &$preferences ) {
        $preferences['hotcat-switch'] = [
            'type' => 'toggle',
            'label-message' => 'Use HotCat for semi-automated categorization',
            'section' => 'editing/editing'
        ];
    }
}