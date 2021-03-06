<?php

namespace WordProofTimestamp\includes\Controller;

use WordProofTimestamp\includes\DomainHelper;
use WordProofTimestamp\includes\OptionsHelper;
use WordProofTimestamp\includes\PostMetaHelper;

class TimestampController {
	public function __construct() {
		add_action( 'wp_ajax_wordproof_get_hash_by_id', array( $this, 'getHashById' ) );
		add_action( 'wp_ajax_wordproof_get_raw_by_id', array( $this, 'getRawById' ) );
		add_action( 'wp_ajax_wordproof_save_timestamp', array( $this, 'saveTimestampAjax' ) );
	}

	public static function saveTimestamp( $postId, $chain, $transactionId, $remote = false ) {

		$metaFields = HashController::getFields( $postId );

		$meta               = $metaFields['properties'];
		$meta['attributes'] = $metaFields['attributes'];

		$meta['blockchain']    = $chain;
		$meta['transactionId'] = $transactionId;
		$meta['hash']          = HashController::getHash( $postId );

		PostMetaHelper::savePostMeta( $postId, $meta, $remote );

		echo json_encode( array(
			'success' => true,
			'data'    => array(
				'url' => DomainHelper::getPermalink( $postId ) . '#wordproof'
			),
		) );
		exit;
	}

	public function saveTimestampAjax() {
		check_ajax_referer( 'wordproof', 'security' );

        if (!isset($_REQUEST['post_id']) || !isset($_REQUEST['transaction_id']))
            return;

		$postId        = intval( sanitize_text_field( wp_unslash( ($_REQUEST['post_id']) ? $_REQUEST['post_id'] : 0 ) ) );
		$chain         = OptionsHelper::get('network');
		$transactionId = sanitize_text_field( wp_unslash( ($_REQUEST['transaction_id']) ? $_REQUEST['transaction_id'] : '' ) );

		self::saveTimestamp( $postId, $chain, $transactionId );

		echo json_encode( array(
			'success' => true,
			'data'    => array(
				'url' => DomainHelper::getPermalink( $postId ) . '#wordproof'
			),
		) );
		exit;
	}

	public function getHashById() {
		check_ajax_referer( 'wordproof', 'security' );

        if (!isset($_REQUEST['post_id']))
            return;

		$postId = intval( $_REQUEST['post_id'] );
		$hash   = HashController::getHash( $postId );
		echo json_encode( $hash );
		exit;
	}

	public function getRawById() {
		check_ajax_referer( 'wordproof', 'security' );

		if (!isset($_REQUEST['post_id']))
		    return;

		$postId = intval( $_REQUEST['post_id'] );
		$json   = HashController::getRawPosts( $postId );
		echo json_encode($json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		exit;
	}
}
