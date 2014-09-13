<?php

namespace Flow\Data;

use Flow\Container;
use Flow\Exception\InvalidDataException;
use Flow\FlowActions;
use Flow\Model\PostRevision;
use Flow\Model\Workflow;
use Flow\WatchedTopicItems;
use Title;
use User;
use WatchedItem;

/**
 * Auto-watch topics when the user performs one of the actions specified
 * in the constructor.
 */
abstract class AbstractTopicInsertListener {
	/**
	 * @param string $changeType
	 * @param Workflow $workflow
	 */
	abstract protected function onAfterInsertExpectedChange( $changeType, Workflow $workflow );

	public function onAfterInsert( $object, array $row, array $metadata ) {
		if ( !$object instanceof PostRevision ) {
			wfWarn( __METHOD__ . ': Object is no PostRevision instance' );
			return;
		}

		if ( !isset( $metadata['workflow'] ) ) {
			wfWarn( __METHOD__ . ': Missing required metadata: workflow' );
			return;
		}
		$workflow = $metadata['workflow'];
		if ( !$workflow instanceof Workflow ) {
			throw new InvalidDataException( 'Workflow metadata is not Workflow instance' );
		}

		if ( $workflow->getType() !== 'topic' ) {
			wfWarn( __METHOD__ . ': Expected "topic" workflow but received "' . $workflow->getType() . '"' );
			return;
		}

		/** @var $title Title */
		$title = $workflow->getArticleTitle();
		if ( !$title ) {
			return;
		}

		$this->onAfterInsertExpectedChange( $row['rev_change_type'], $metadata['workflow'] );
	}

	/**
	 * Returns an array of user ids to subscribe to the title.
	 *
	 * @param string $changeType
	 * @param string $watchType Key of the corresponding 'watch' array in FlowActions.php
	 * @param array $params Params to feed to callback function that will return
	 *   an array of users to subscribe
	 * @return User[]
	 */
	public static function getUsersToSubscribe( $changeType, $watchType, array $params = array() ) {
		/** @var FlowActions $actions */
		$actions = Container::get( 'flow_actions' );

		// Find users defined for this action, in FlowActions.php
		try {
			$users = $actions->getValue( $changeType, 'watch', $watchType );
		} catch ( \Exception $e ) {
			return array();
		}

		// Null will be returned if nothing is defined for this changeType
		if ( !$users ) {
			return array();
		}

		// Some actions may have more complex logic to determine watching users
		if ( is_callable( $users ) ) {
			$users = call_user_func_array( $users, $params );
		}

		return $users;
	}

	// do nothing
	public function onAfterLoad( $object, array $new ) {}
	public function onAfterUpdate( $object, array $old, array $new, array $metadata ) {}
	public function onAfterRemove( $object, array $old, array $metadata ) {}
}

/**
 * Class to immediately subscribe users to the article title when one of the
 * actions specified in the constructor is inserted.
 */
class ImmediateWatchTopicListener extends AbstractTopicInsertListener {
	/**
	 * @var WatchedTopicItems
	 */
	protected $watchedTopicItems;

	/**
	 * @param WatchedTopicItems $watchedTopicItems Helper class for watching titles
	 */
	public function __construct( WatchedTopicItems $watchedTopicItems ) {
		$this->watchedTopicItems = $watchedTopicItems;
	}

	/**
	 * @param string $changeType
	 * @param Workflow $workflow
	 */
	public function onAfterInsertExpectedChange( $changeType, Workflow $workflow ) {
		$users = static::getUsersToSubscribe( $changeType, 'immediate', array( $this->watchedTopicItems ) );

		foreach ( $users as $user ) {
			$title = $workflow->getArticleTitle();

			WatchedItem::fromUserTitle( $user, $title )->addWatch();
			$this->watchedTopicItems->addOverrideWatched( $title );
		}
	}

	/**
	 * @param WatchedTopicItems $watchedTopicItems
	 * @return User[]
	 */
	public static function getCurrentUser( WatchedTopicItems $watchedTopicItems ) {
		return array( $watchedTopicItems->getUser() );
	}
}
