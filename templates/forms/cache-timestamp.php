<?php
/**
 * The field template for the current cache timestamp.
 *
 * @package MITlib Pull Hours
 * @since 0.0.2
 */

// Convert timestamp to the right timezone.
$cache_timestamp = get_option( 'cache_timestamp' );
$last_updated = new DateTime( gmdate( 'M j, Y g:i:s A T', $cache_timestamp ) );
$last_updated->setTimezone( new DateTimeZone( 'America/New_York' ) );
echo esc_html( $last_updated->format( 'M j, Y g:i:s A T' ) );
