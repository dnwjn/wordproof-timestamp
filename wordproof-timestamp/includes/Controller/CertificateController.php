<?php

namespace WordProofTimestamp\includes\Controller;

use WordProofTimestamp\includes\PostMetaHelper;

class CertificateController
{

  private static $default_template = "<div><p class='wordproof-certificate-link' style='display: none; align-items: center;'><img src='" . WORDPROOF_URI_IMAGES . "/wordproof-icon.png" . "' style='max-width: 30px; max-height: 30px; display: inline-block; margin: 0 10px 0 0;' alt='WordProof timestamp'/><a class='wordproof-certificate-helper' data-post-id='POST_ID' href='CERTIFICATE_URL'>CERTIFICATE_TEXT</a></p></div>";
  private static $default_text = "View this content's WordProof Timestamp certificate";
  private static $default_url = '#wordproof';

  public function __construct()
  {
    add_filter('the_content', array($this, 'addCertificateLink'));
    add_action('wp_footer', array($this, 'addCertificateModalContainer'));
    add_action('wp_enqueue_scripts', array($this, 'addCertificateScript'));
    add_filter('script_loader_tag', [$this, 'addCertificateScriptAttribute'], 10, 2);
    add_action('wp_head', array($this, 'addCertificateSchema'));
    add_action('wordproof_translate_certificate', array($this, 'translateCertificate'));
  }

  public function addCertificateLink($content)
  {
    global $post;

    if (!empty($post)) {
      $meta = PostMetaHelper::getPostMeta($post->ID, ['date', 'blockchain']);
      if (isset($meta->date) && !empty($meta->blockchain)) {
        $content .= $this->getCertificateLinkHtml($post->ID);
      }
    }

    return $content;
  }

  public function addCertificateModalContainer()
  {
    global $post;

    if (!empty($post)) {
      $meta = PostMetaHelper::getPostMeta($post->ID, ['date', 'blockchain']);
      if (isset($meta->date) && !empty($meta->blockchain)) {
        echo '<div id="wordproof-certificate-container"></div>';
      }
    }
  }

  public function addCertificateScript()
  {
    if (is_singular()) {
      global $post;
      $meta = PostMetaHelper::getPostMeta($post->ID, ['date', 'blockchain']);

      if (isset($meta->date) && !empty($meta->blockchain)) {
        $wsfyOptions = get_option('wordproof_wsfy');
        $wsfyOptions = (isset($wsfyOptions['active']) && $wsfyOptions['active'] === true) ? ['active' => $wsfyOptions['active'], 'noRevisions' => $wsfyOptions['noRevisions'], 'siteId' => $wsfyOptions['siteId']] : '';
        $certificateText = $this->getCertificateText();
        $certificateDOMParent = get_option('wordproof_certificate_dom_selector');

        wp_enqueue_script('wordproof.frontend.js', WORDPROOF_URI_JS . '/frontend.js', array(), filemtime(WORDPROOF_DIR_JS . '/frontend.js'), true);
        wp_localize_script('wordproof.frontend.js', 'wproof', array(
          'uid' => $post->ID,
          'css' => WORDPROOF_URI_CSS . '/frontend.css',
          'icon' => WORDPROOF_URI_IMAGES . '/wordproof-icon.png',
          'logo' => WORDPROOF_URI_IMAGES . '/wordproof-logo.png',
          'wsfy' => $wsfyOptions,
          'certificateText' => $certificateText,
          'certificateDOMParent' => (isset($certificateDOMParent)) ? $certificateDOMParent : ''
        ));
        do_action('wordproof_translate_certificate');
      }
    }
  }

  public function translateCertificate()
  {
    wp_localize_script('wordproof.frontend.js', 'wproofStrings', array(
      'title' => __('Timestamp Certificate', 'wordproof-timestamp'),
      'subtitle' => __('Protected with', 'wordproof-timestamp'),
      'readMore' => __('Read More', 'wordproof-timestamp'),
      'switchRaw' => __('Raw', 'wordproof-timestamp'),
      'switchArticle' => __('Article', 'wordproof-timestamp'),
      'switchAbout' => __('About this Timestamp Certificate', 'wordproof-timestamp'),
      'switchAboutReturn' => __('Back to Timestamp Certificate', 'wordproof-timestamp'),
      'blockchainLink' => __('View on the blockchain', 'wordproof-timestamp'),
      'timestampChecker' => __('Timestamp Checker', 'wordproof-timestamp'),
      'dateModification' => __('Modification Date', 'wordproof-timestamp'),
      'promotionLinkText' => __('Protect your content on the blockchain with WordProof Timestamp', 'wordproof-timestamp'),
      'buttonPrevious' => __('Previous', 'wordproof-timestamp'),
      'buttonNext' => __('Next', 'wordproof-timestamp'),
      'aboutTitle' => __('What is this Timestamp Certificate', 'wordproof-timestamp'),
      'aboutText' => sprintf(
        __('This content is protected with WordProof, a new web standard for a more trustworthy internet. This timestamp exists of a unique hash (summary) based on the title, date and content of this page. It is stored in the blockchain and can never be altered.<br/><br/>You can verify this Timestamp Certificate yourself with the <a target="_blank" rel="noopener noreferrer" href="%s">WordProof Timestamp Checker</a>. The hash of this post is', 'wordproof-timestamp')
        , 'https://wordproof.io/check/'),
    ));
  }

  public function addCertificateScriptAttribute($tag, $handle)
  {
    if ($handle !== 'wordproof.frontend.js')
      return $tag;
    return str_replace(' src', ' defer="defer" src', $tag);
  }

  public function addCertificateSchema()
  {
    if (is_singular()) {
      global $post;
      echo SchemaController::getSchema($post->ID);
    }
  }

  /**
   * Generate certificate html link
   * @param $postId
   * @return mixed|string
   */
  private function getCertificateLinkHtml($postId)
  {
    $html = $this->getCertificateTemplate();
    $text = $this::getCertificateText();
    $url = $this->getCertificateUrl();
    $html = str_replace('CERTIFICATE_URL', $url, $html);
    $html = str_replace('CERTIFICATE_TEXT', $text, $html);
    $html = str_replace('POST_ID', $postId, $html);
    return $html;
  }

  public static function getCertificateText()
  {
    $text = get_option('wordproof_certificate_text', null) ?: self::$default_text;
    return stripslashes($text);
  }

  private function getCertificateTemplate()
  {
    $template = self::$default_template;
    return $template;
  }


  private function getCertificateUrl()
  {
    $url = self::$default_url;
    return $url;
  }
}
