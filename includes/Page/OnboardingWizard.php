<?php

namespace WordProofTimestamp\includes\Page;

use WordProofTimestamp\includes\OptionsHelper;

/**
 * Class SettingsPage
 * @package WordProofTimestamp\includes\Page
 */
class OnboardingWizard {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'addSettingsPage' ) );
	}

	public function addSettingsPage() {
		add_submenu_page(
			'wordproof-dashboard',
			'WordProof Wizard',
			'Setup',
			'manage_options',
			'wordproof-wizard',
			array( $this, 'generateSettingsPage' ),
			4
		);
	}

	public function generateSettingsPage() {
        $assetVersion = (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'local') ? null : WORDPROOF_VERSION;

		wp_enqueue_style( 'wordproof.wizard.css', WORDPROOF_URI_CSS . '/wizard.css', array(), $assetVersion );
		wp_enqueue_script( 'wordproof.wizard.js', WORDPROOF_URI_JS . '/wizard.js', array(), $assetVersion, true );

		$currentValues = array_merge(
			[
				'certificate_text' => OptionsHelper::get('certificate_text'),
				'isWSFYActive'     => OptionsHelper::isWSFYActive()
			],
			(array) OptionsHelper::getWSFY(),
			(array) OptionsHelper::getOAuth( [] )
		);

		wp_localize_script( 'wordproof.wizard.js', 'wordproof', [
			'urls'                => [
				'dashboard' => admin_url( 'admin.php?page=wordproof-dashboard' ),
				'settings'  => admin_url( 'admin.php?page=wordproof-settings' ),
				'api'       => WORDPROOF_API_URI,
				'images'    => WORDPROOF_URI_IMAGES,
				'signup'    => WORDPROOF_MY_URI . 'signup?plan=free&url=' . get_site_url(),
				'site'      => get_site_url(),
			],
			'ajax'                => [
				'url'      => admin_url( 'admin-post.php' ),
				'security' => wp_create_nonce( 'wordproof' ),
			],
			'currentValues'       => $currentValues,
			'registeredPostTypes' => array_values( get_post_types( [ 'public' => true ] ) ),
		] );

		?>
		<div id="wordproof-onboarding-wizard"></div>

		<?php
	}
}
