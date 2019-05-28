<?php

namespace WordProofTimestampFree\includes;


class TimestampAjaxHelper
{

  public function __construct()
  {
    add_action('wp_ajax_wordproof_save_meta', array($this, 'saveMeta'));
    add_action('wp_ajax_wordproof_get_hash_by_id', array($this, 'getHashById'));
    add_action('wp_ajax_wordproof_get_post_by_id', array($this, 'getPostById'));
  }

  public function saveMeta()
  {
    check_ajax_referer('wordproof', 'security');

    $postId = intval($_REQUEST['post_id']);
    $meta = TimestampHelper::buildPostMetaArray($_REQUEST['date'], $_REQUEST['title'], $_REQUEST['content'], $_REQUEST['transaction_id'], $_REQUEST['block_num'], $_REQUEST['block_time'], $_REQUEST['network'], $_REQUEST['hash']);
    if (!empty($meta)) {

      TimestampHelper::saveTimestampPostMeta($postId, $meta);

      echo json_encode(array(
        'success' => true,
        'data' => array(
          'url' => get_permalink($postId) . '#wordproof'
        ),
      ));
      exit;
    }

    echo json_encode(array(
      'success' => false,
      'message' => 'Meta array is empty'
    ));
    exit;
  }

  public function getHashById()
  {
    check_ajax_referer('wordproof', 'security');
    $postId = intval($_REQUEST['post_id']);
    $hash = TimestampHelper::generatePostHashById($postId);
    echo json_encode($hash);
    exit;
  }

  public function getPostById()
  {
    check_ajax_referer('wordproof', 'security');
    $postId = intval($_REQUEST['post_id']);

    if (current_user_can('manage_options') && !empty($postId)) {
      $post = get_post($postId);

//      if (!empty($post)) {
//        $post['permalink'] = get_permalink($postId);
//      }

      echo json_encode($post);
      exit;
    }
  }
}