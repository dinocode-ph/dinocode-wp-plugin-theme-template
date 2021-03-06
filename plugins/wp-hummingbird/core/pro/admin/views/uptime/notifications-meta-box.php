<?php
/**
 * Uptime notifications meta box.
 *
 * @since 1.9.3
 * @package Hummingbird
 *
 * @var string $downtime_url     URL to downtime page.
 * @var string $notice_class     Class for the notice.
 * @var string $notice_message   Message for the notice.
 * @var array  $reports_settings Settings for Uptime Reports.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pending = false;

?>

<p>
	<?php
	esc_html_e(
		'Our advanced uptime API pings this website every 2 minutes to see if everything is OK. This
	feature sends an email to nominated recipients whenever your website is very slow, or completely down.',
		'wphb'
	);
	?>
</p>

<div class="sui-notice sui-notice-<?php echo esc_attr( $notice_class ); ?>">
	<p><?php echo esc_html( $notice_message ); ?></p>
</div>

<form method="post" id="wphb-uptime-reporting" class="wphb-report-settings" data-module="uptime" data-name="Uptime Notifications">
	<div class="sui-box-settings-row">
		<div class="sui-box-settings-col-1">
			<span class="sui-settings-label"><?php esc_html_e( 'Configure', 'wphb' ); ?></span>
			<span class="sui-description">
			<?php esc_html_e( 'Choose who you want to receive uptime email notifications when your website becomes unavailable.', 'wphb' ); ?>
		</span>
		</div>
		<div class="sui-box-settings-col-2">
			<input type="hidden" name="scheduled-reports" value="0">
			<label class="sui-toggle">
				<input type="checkbox" name="scheduled-reports" value="1" <?php checked( $reports_settings['enabled'] ); ?> id="chk1">
				<span class="sui-toggle-slider"></span>
			</label>
			<label for="chk1"><?php esc_html_e( 'Send an email notification when this website goes down', 'wphb' ); ?></label>
			<div class="sui-border-frame sui-toggle-content schedule-box <?php echo $reports_settings['enabled'] ? '' : 'sui-hidden'; ?>">
				<div class="sui-recipients">
					<label class="sui-label"><?php esc_html_e( 'Recipients', 'wphb' ); ?></label>
					<?php if ( count( $reports_settings['recipients'] ) ) : ?>
						<div class="sui-notice sui-notice-warning sui-no-margin-top wphb-no-recipients sui-hidden">
							<p><?php esc_html_e( "You've removed all recipients. If you save without a recipient, we'll automatically turn off notifications.", 'wphb' ); ?></p>
						</div>
						<?php
						foreach ( $reports_settings['recipients'] as $key => $value ) :
							$input_value        = new stdClass();
							$input_value->name  = $value['name'];
							$input_value->email = $value['email'];
							$input_value        = wp_json_encode( $input_value );

							$tooltip = __( 'Subscribed', 'wphb' );
							if ( isset( $value['is_pending'] ) && $value['is_pending'] ) {
								$pending = true;
								$tooltip = __( 'Awaiting confirmation', 'wphb' );
							}
							?>
							<div class="sui-recipient">
								<span class="sui-recipient-status sui-tooltip" data-tooltip="<?php echo esc_attr( $tooltip ); ?>">
									<?php if ( isset( $value['is_pending'] ) && $value['is_pending'] ) : ?>
										<i class="sui-icon-clock" aria-hidden="true"></i>
									<?php else : ?>
										<i class="sui-icon-check-tick" aria-hidden="true"></i>
									<?php endif; ?>
								</span>
								<input data-id="<?php echo esc_attr( $key ); ?>" type="hidden" id="report-recipient" name="report-recipients[]" value="<?php echo esc_attr( $input_value ); ?>">
								<span class="sui-recipient-name"><?php echo esc_html( $value['name'] ); ?></span>
								<span class="sui-recipient-email"><?php echo esc_html( $value['email'] ); ?></span>
								<?php if ( $pending ) : ?>
									<button type="button" class="sui-button-icon wphb-resend-recipient sui-tooltip" data-tooltip="<?php esc_attr_e( 'Resend email', 'wphb' ); ?>" data-name="<?php echo esc_attr( $value['name'] ); ?>" data-email="<?php echo esc_attr( $value['email'] ); ?>">
										<i class="sui-icon-send" aria-hidden="true"></i>
									</button>
								<?php endif; ?>
								<button type="button" class="sui-button-icon wphb-remove-recipient <?php echo $pending ? '' : 'wphb-not-pending-recipient'; ?>">
									<i class="sui-icon-trash" aria-hidden="true"></i>
								</button>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<div class="sui-notice sui-notice-warning sui-no-margin-top wphb-no-recipients sui-hidden">
							<p><?php esc_html_e( "You've removed all recipients. If you save without a recipient, we'll automatically turn off notifications.", 'wphb' ); ?></p>
						</div>
					<?php endif; ?>
				</div>
				<a class="sui-button sui-button-ghost sui-add-recipient" data-modal-open="wphb-add-recipient-modal" data-modal-open-focus="reporting-first-name" data-modal-mask="true">
					<i class="sui-icon-plus" aria-hidden="true"></i>
					<?php esc_html_e( 'Add Recipient', 'wphb' ); ?>
				</a>
				<div class="sui-notice sui-hidden" id="wphb-confirm-sub-notice">
					<p><?php esc_html_e( 'Recipients must confirm their subscription to begin receiving emails.', 'wphb' ); ?></p>
				</div>

				<div class="sui-notice<?php echo ( $pending && count( $reports_settings['recipients'] ) ) ? '' : ' sui-hidden'; ?>" id="wphb-pending-sub-notice">
					<p><?php esc_html_e( "Some recipients haven't confirmed their subscription to this email report. You can resend the confirmation email or remove them from the list.", 'wphb' ); ?></p>
				</div>
				<div class="sui-form-field">
					<label for="threshold" class="sui-label"><?php esc_html_e( 'Threshold', 'wphb' ); ?></label>
					<select id="threshold" name="threshold">
						<option <?php selected( 0, $reports_settings['threshold'] ); ?> value="0">
							<?php esc_html_e( 'Instant', 'wphb' ); ?>
						</option>
						<option <?php selected( 5, $reports_settings['threshold'] ); ?> value="5">
							5 <?php esc_html_e( 'Minutes', 'wphb' ); ?>
						</option>
						<option <?php selected( 10, $reports_settings['threshold'] ); ?> value="10">
							10 <?php esc_html_e( 'Minutes', 'wphb' ); ?>
						</option>
						<option <?php selected( 30, $reports_settings['threshold'] ); ?> value="30">
							30 <?php esc_html_e( 'Minutes', 'wphb' ); ?>
						</option>
					</select>
				</div>
				<span class="sui-description">
				<?php
				printf(
					/* translators: %1$s: opening a tag, %2$s: closing a tag */
					esc_html__( "We won't notify you if your website becomes available again within the specified timeframe. All downtimes are still recorded in the %1\$sdowntime report%2\$s, you just won't get notified.", 'wphb' ),
					'<a href="' . esc_url( $downtime_url ) . '">',
					'</a>'
				);
				?>
			</span>
			</div>
		</div>
	</div>