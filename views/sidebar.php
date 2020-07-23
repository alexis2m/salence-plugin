<?php

?>

<div class="postbox">
	<h3 class="hndle">
		<span><?php esc_html_e('Useful Salence Links', 'salence-pixel'); ?></span>
	</h3>
	<div class="inside">
		<p>
			<?php esc_html_e('Here are some links related to Salence.io:', 'salence-pixel'); ?>
		</p>
		<p>
			<?php
			printf(
				'<a href="%1$s" target="_blank">%2$s</a> - %3$s',
				esc_url('https://salence.io'),
				esc_html__('Salence.io', 'salence-pixel'),
				esc_html__('Home page of the Salence.io website', 'salence-pixel')
			);
			?>
		</p>
		<p>
			<?php
			printf(
				'<a href="%1$s" target="_blank">%2$s</a> - %3$s',
				esc_url('https://salence.io/dashboard'),
				esc_html__('Dashboard', 'salence-pixel'),
				esc_html__('Directly access to your Salence dashboard', 'salence-pixel')
			);
			?>
		</p>
		<p>
			<?php
			printf(
				'<a href="%1$s" target="_blank">%2$s</a> - %3$s',
				esc_url('https://salence.io/pages/wordpress-integration'),
				esc_html__('Guide', 'salence.io'),
				esc_html__('Quickly learn how to integrate Salence to your WordPress website', 'salence-pixel')
			);
			?>
		</p>
		<p>
			<?php
			printf(
				'<a href="%1$s" target="_blank">%2$s</a> - %3$s',
				esc_url('https://alexismaison.com/'),
				esc_html__('Alexis Maison', 'salence-pixel'),
				esc_html__('The author website, just one click away', 'salence-pixel')
			);
			?>
		</p>
	</div>
</div>
