<?php

elgg_load_css('hybridauth.css');

$user = elgg_get_page_owner_entity();

$providers = unserialize(elgg_get_plugin_setting('providers', 'elgg_hybridauth'));

foreach ($providers as $provider => $settings) {

	if ($settings['enabled']) {

		$adapter = false;

		$ha = new ElggHybridAuth();

		try {
			$adapter = $ha->getAdapter($provider);
		} catch (Exception $e) {
			// do nothing
		}

		if ($adapter) {

			$title = elgg_view_image_block(elgg_view_icon(strtolower("auth-$provider")), $provider);

			if (elgg_get_plugin_user_setting("$provider:uid", $user->guid, 'elgg_hybridauth')) {
				$mod = '<p class="hybridauth-diagnostics-success">' . elgg_echo('hybridauth:provider:user:authenticated') . '</p>';
				$mod .= elgg_view('output/url', array(
					'href' => "action/hybridauth/deauthorize?provider=$provider&guid=$user->guid",
					'is_action' => true,
					'text' => elgg_echo('hybridauth:provider:user:deauthorize'),
					'class' => 'elgg-button elgg-button-action'
						));

				$mod .= elgg_view("hybridauth/accounts/$provider");
				
			} else {
				$forward_url = urlencode(elgg_normalize_url("hybridauth/accounts/$user->username"));
				$mod = elgg_view('output/url', array(
					'href' => "hybridauth/authenticate?provider=$provider&elgg_forward_url=$forward_url",
					'text' => elgg_echo('hybridauth:provider:user:authenticate'),
					'class' => 'elgg-button elgg-button-action'
						));
			}

			echo '<div class="hybridauth-user-account-settings">'.elgg_view_module('info', $title, $mod).'</div>';
		}
	}
}
echo '<div class="clearfix"> </div>';
