<?php
/**
 * The field template for the current cache timestamp.
 *
 * @package MITlib Pull Hours
 * @since 0.0.2
 */

echo esc_html( date( 'M j, Y g:i:s A T', $cache_timestamp ) );
