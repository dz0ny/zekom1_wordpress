<?php

function add_settings_section( $id, $title, $callback, $page ){
  global $wp_test_expectations;
  $wp_test_expectations["wp_settings_sections"][$id] = [$callback, $page];
}

function add_settings_field( $id, $title, $callback, $page, $section, $args )
{
  global $wp_test_expectations;
  $wp_test_expectations["wp_settings_fields"][$id] = [$callback, $page];
}

function register_setting($option_group, $option_name, $sanitize_callback = '')
{
  global $wp_test_expectations;
  $wp_test_expectations["wp_settings"][$option_name] = $option_group;
}