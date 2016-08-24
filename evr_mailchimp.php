<?php 
/*
Plugin Name: Event Registration MailChimp Addon
Plugin URI: http://gtmanagement.com
Description: This wordpress plugin adds MailChimp functionality to the Event Registration Plugin. 
Version: 1.00
Author: Glenn Tate - GT Management Systems
Author URI: http://gtmanagement.com
*/

/*  Copyright 2012  Glenn Tate  (email : gt@gtmanagement.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/*
Todo:
 
*/
//Define path variables
//require ("evr_mailchimp_options.php");


class MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ),15 );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
	$par = "/event-registration/EVNTREG.php";
        // This page will be under "Settings"
        #add_options_page(
		#'Settings Admin',
		  add_submenu_page (
          	'/event-registration/EVNTREG.php',
            'MailChimp Config', 
			'MailChimp Config',
			'manage_options', 
            'evr-mailchimp-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'evr-mailchimp-option' );
        ?>
        <div class="wrap">
            <?php screen_icon(options-general); ?>
            <h2>Event Registration MailChimp</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'evr_mailchimp_group' );   
                do_settings_sections( 'evr-mailchimp-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'evr_mailchimp_group', // Option group
            'evr-mailchimp-option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'MailChimp Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'evr-mailchimp-admin' // Page
        );  

        add_settings_field(
            'api_number', // ID
            'API Number', // Title 
            array( $this, 'api_number_callback' ), // Callback
            'evr-mailchimp-admin', // Page
            'setting_section_id' // Section           
        );      

        add_settings_field(
            'list_id', 
            'List ID', 
            array( $this, 'list_id_callback' ), 
            'evr-mailchimp-admin', 
            'setting_section_id'
        );
		add_settings_field(
            'grouping_name', 
            'Groups Name', 
            array( $this, 'grouping_name_callback' ), 
            'evr-mailchimp-admin', 
            'setting_section_id'
        );      
		
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
	 * $new_input['id_number'] = absint( $input['id_number'] );
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['api_number'] ) )
            $new_input['api_number'] = sanitize_text_field( $input['api_number'] );

        if( isset( $input['list_id'] ) )
            $new_input['list_id'] = sanitize_text_field( $input['list_id'] );
			
		if( isset( $input['grouping_name'] ) )
            $new_input['grouping_name'] = sanitize_text_field( $input['grouping_name'] );	

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function api_number_callback()
    {
        printf(
            '<input type="text" id="api_number" size="40" name="evr-mailchimp-option[api_number]" value="%s" />',
            isset( $this->options['api_number'] ) ? esc_attr( $this->options['api_number']) : ''
        );
		echo ' Login to your MailChimp account and get an <a href="https://us4.admin.mailchimp.com/account/api/" target="_blank">API key</a>.';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function list_id_callback()
    {
        printf(
            '<input type="text" id="list_id" size="15" name="evr-mailchimp-option[list_id]" value="%s" />',
            isset( $this->options['list_id'] ) ? esc_attr( $this->options['list_id']) : ''
        );
		echo ' The List ID is located under the Settings > List name & defaults menu in the MailChimp Dashboard.';
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function grouping_name_callback()
    {
        printf(
			'<input type="text" id="grouping_name" size="15" name="evr-mailchimp-option[grouping_name]" value="%s" />',
            isset( $this->options['grouping_name'] ) ? esc_attr( $this->options['grouping_name']) : ''
        );
		echo ' Enter the name for the Groups heading for example: Events. The subscribers will be grouped by the Event Name.';
    }
}

if( is_admin() )
    $my_settings_page = new MySettingsPage();
	
require ('mail_chimp_list.php');	
?>