<?php
/*
Plugin Name: spambomb
Description: Delete SPAM Comments with SPAM BOMB.
Version: 2.2
Author: Max-Web
Author URI: https://maxweb.co
Requires at least: 5.1
Requires PHP: 5.6
License: GPLv2
Text Domain: spambomb

SPAM BOMB is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
SPAM BOMB is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with SPAM BOMB. If not, see https://www.gnu.org/licenses/gpl-2.0.html

*/

//Block Direct Access of PHP Files
defined( 'ABSPATH' ) or die( 'No Script Kiddies Please!' );

//Function to enqueue plugin stylesheet

function spambomb_plugin_stylesheet()
{
    $spambomb_plugin_stylesheet_url = plugin_dir_url( __FILE__ ) . 'css/spambomb-style.css';
    wp_enqueue_style( 'spambomb-style', $spambomb_plugin_stylesheet_url, array(), '1.0.1', 'all' );
}


//Hook the stylesheet to the admin_enqueue_scripts Hook and show the styles in the admin page
add_action( 'admin_enqueue_scripts', 'spambomb_plugin_stylesheet' );


//Enqueue Javascript

function spambomb_plugin_script()
{
    $spambomb_plugin_script_url = plugin_dir_url( __FILE__ ) . 'js/spambombscript.js';
    wp_enqueue_script( 'spambombscript', $spambomb_plugin_script_url, array('jquery', 'jquery-ui-dialog') );
    wp_enqueue_style( 'jquery' );
    wp_enqueue_style( 'wp-jquery-ui-dialog' );
}

//Inilialize Javascript in the admin interface

add_action( 'admin_enqueue_scripts', 'spambomb_plugin_script' );


function spambomb_add_comments_page()
{
    //Check if the current user is logged-in && on an admin page

    if ( is_user_logged_in() && is_admin() )
    {
        // check if the plugin is active, continue. An error will appear if the plugin is not active

        $spambomb_plugin_active_dir = plugin_dir_url( __FILE__ );
        is_plugin_active( $spambomb_plugin_active_dir );
    }
    else 
    {
    wp_die('You are not allowed to access this plugin! Please log-in to WordPress');
    }

    //Check user capability and show an error if the user does not have the correct privaleges to acccess the plugin

    if ( current_user_can( 'edit_posts', 'manage_options' ) )
    {
    //Continue to run the plugin
    }
    else 
    {
        wp_die( 'SPAMBOMB PLUGIN ERROR: Your WordPress Admin Dashboard has been deactivated because you are not an Editor or an Administrator. Please ask the owner of the website to change your user role to Editor|Administrator.' );
    }

    //Print an admin notice for SPAMBOMB

    //Add a new sub-page to the comments menu

    add_comments_page( 'SPAMBOMB DELETE', 'SPAMBOMB DELETE', 'publish_posts',  'spambomb-delete-comments', 'spambomb_delete_comments_page' );

    // PAGE CONTENT CALLBACK FUNCTION

    function spambomb_delete_comments_page()
    {

        echo "<div class='wrap'> ";

        // Get User Nickname and show it above the SPAM FORM
        echo "<div class='spambomb-welcome-message'>";
        echo "<div class='reload-data-spambomb'>";
        echo "<form action='' method='post'>";
        echo "<input type='submit' name'reload-data-spambomb-btn' class='button primary-button spambomb-submit refresh' value='REFRESH PAGE'>";
        echo "</form>";
        echo "</div>";
        $spambomb_user = get_user_meta( get_current_user_id(), 'nickname', true );
        echo "<h2 class='h2 wp-heading-inline'> WELCOME TO SPAMBOMB " . strtoupper($spambomb_user) . " </h2>";
        echo "<p id='spambomb-date'><span class='dashicons dashicons-calendar'></span> " . date("l jS \of F Y") . " </p>";
        echo "</div>";
    
        // Show the comment count that exist in the database 

        ?>
        <div class="spambomb-total-comments">
        <?php $spambomb_comment_count = wp_count_comments(); ?>
        <div class="spambomb-comment-section">

            <div class="spambomb-comment-section-1">
            <h2 class='total-comments-text'> Total Comments<br><span class='spambox-count-value'>= <?php echo $spambomb_comment_count->total_comments ?> =</span></h2>
            </div>

            <div class="spambomb-comment-section-2">
            <h2 class='total-comments-text'> Pending Comments<br><span class='spambox-count-value'>= <?php echo $spambomb_comment_count->moderated ?> =</span></h2>
            </div>

            <div class="spambomb-comment-section-3">
            <h2 class='total-comments-text'> Spam Comments<br><span class='spambox-count-value'>= <?php echo $spambomb_comment_count->spam ?> =</span></h2>
            </div>

            <div class="spambomb-comment-section-4">
            <h2 class='total-comments-text'> Trash Comments<br><span class='spambox-count-value'>= <?php echo $spambomb_comment_count->trash ?> =</span></h2>
            </div>

            <div class="spambomb-comment-section-5">
            <h2 class='total-comments-text'> Approved Comments<br><span class='spambox-count-value'>= <?php echo $spambomb_comment_count->approved ?> =</span></h2>
            </div>

        </div>
        </div>
        <?php 

        echo "<div class='spambomb-error-log'>";
        echo "<p class='spambomb-error-log-text'> View Error Messages Below </p>";
        echo "</div>";

        if ( isset( $_POST[ 'reload-data-spambomb-btn' ] ) == 1 )
        {
            // Load the page again
        }
        

        // HTML FORM TO SUBMIT SPAM KEYWORDS

        ?>
        <form action="" method="POST" ">
        </p>
        <div class="spambomb-keyword-list ">
        <span style="font-size: 1.4em; color: #000000; font-family: Arial;" class="span-comment-textbox">Enter Spam Keywords To Remove From Pending Comments Below </span>
        <br><br><br>
        <label id="spambomb-field-1"> FIELD 1 => </label><input type="text" name="spambomb-spammer-text1" placeholder="e.g Lose Weight" size="30" class="spambomb-input-field"><br><br>
        <label id="spambomb-field-1"> FIELD 2 => </label><input type="text" name="spambomb-spammer-text2" placeholder="e.g Credit Card" size="30" class="spambomb-input-field"><br><br>
        <label id="spambomb-field-1"> FIELD 3 => </label><input type="text" name="spambomb-spammer-text3" placeholder="e.g Additional Income" size="30" class="spambomb-input-field"><br><br>
        <br>
        <div class="spambomb-form-options">
            <h3 style="color: #fff;">COMMENT OPTIONS</h3>
        <input type="radio" name="spambomb-post-delete-spam-comments" value="delete-comments"> <span style="font-size: 1.2em;color:#ffffff;">Delete Spam Comments (Field 1,2,3)</span><br>
        <input type="radio" name="spambomb-post-delete-spam-comments" value="delete-all-comments"> <span style="font-size: 1.2em;color:#ffffff;">Delete All The Comments On Your WP Website </span>
        <br>
        </div>
        <input type="submit" name="spambomb-post-confirm-spam-deletion" class="button primary-button spambomb-submit" value="DELETE COMMENTS"> 
        </form>
        </div>
        <?php

             if ( isset($_POST['spambomb-post-confirm-spam-deletion']) && isset($_POST['spambomb-post-delete-spam-comments']) == 1)
            {
                if ( isset($_POST['spambomb-post-confirm-spam-deletion']) && isset($_POST['spambomb-post-delete-spam-comments']) == 1)
                {
                $spambomb_radio_buttons = $_POST[ 'spambomb-post-delete-spam-comments' ];

                if( $spambomb_radio_buttons == 'delete-comments' )
                {

                // Initialize the Global Database Variable

                global $wpdb;

                // FIRST INPUT FIELD CONDITION

                if ( empty($_POST[ 'spambomb-spammer-text1' ]) == 0)
                {
                    $spambomb_spammer_text1 = sanitize_text_field( ($_POST[ 'spambomb-spammer-text1' ]) );
                    $spambomb_delete_query1 = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_content LIKE  '%$spambomb_spammer_text1%' " );
                    echo "<div class='spambomb-field-empty1'>";
                    echo "<h4 id='spambomb-field-h4'>FIELD 1: SPAM COMMENTS DELETED </h4>";
                    echo "</div>";
                }
                else 
                {
                    echo "<div class='spambomb-field-empty1'>";
                    echo "<h4 id='spambomb-field-h4'>FIELD 1: EMPTY 0 COMMENTS DELETED</h4>";
                    echo "</div>";
                }

                // SECOND INPUT FIELD CONDITION

                if ( empty($_POST[ 'spambomb-spammer-text2' ]) == 0 )
                {
                    global $wpdb;
                    $spambomb_spammer_text2 = sanitize_text_field( ($_POST[ 'spambomb-spammer-text2' ] ));
                    $spambomb_delete_query2 = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_content LIKE '%$spambomb_spammer_text2%' " );
                    echo "<div class='spambomb-field-empty2'>";
                    echo "<h4 id='spambomb-field-h4'>FIELD 2: SPAM COMMENTS DELETED </h4>";
                    echo "</div>";
                }
                else 
                {
                    echo "<div class='spambomb-field-empty2'>";
                    echo "<h4 id='spambomb-field-h4'>FIELD 2: EMPTY 0 COMMENTS DELETED </h4>";
                    echo "</div>";
                }

                // THIRD INPUT FIELD CONDITION

                if ( empty($_POST[ 'spambomb-spammer-text3' ]) == 0 )
                {
                    global $wpdb;
                    $spambomb_spammer_text3 = sanitize_text_field( ($_POST[ 'spambomb-spammer-text3' ]) );
                    $spambomb_delete_query3 = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_content LIKE '%$spambomb_spammer_text3%' " );
                    echo "<div class='spambomb-field-empty3'>";
                    echo "<h4 id='spambomb-field-h4'>FIELD 3: SPAM COMMENTS DELETED </h4>";
                    echo "</div>";
                }
                else 
                {
                    echo "<div class='spambomb-field-empty3'>";
                    echo "<h4 id='spambomb-field-h4'>FIELD 3: EMPTY  0 COMMENTS DELETED </h4>";
                    echo "</div>";
                }
            }
        }
        }

        // DELETE ALL COMMENTS WHEN CHECKBOX SELECTED

        global $wpdb;

        if ( isset($_POST[ 'spambomb-post-delete-spam-comments' ]) && !empty($_POST[ 'spambomb-post-confirm-spam-deletion' ]) )
        {
            
        $spambomb_radio_buttons = $_POST[ 'spambomb-post-delete-spam-comments' ];
         if( $spambomb_radio_buttons == 'delete-all-comments' )
        {
        $spambomb_delete_everything_query = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved LIKE  0" );
        echo "<div id='spambombdialog' title='SPAMBOMB NOTIFICATION'>
        <p style='font-size: 1.2em;'> All the Pending Comments on your WordPress website has been successfully deleted.
        Please click the 'REFRESH PAGE' button to view the updated results.
        <br> <h3 style='color: royalblue;'>The Max-Web Team</h3>
        </p>
        </div>";
        wp_cache_flush(  );
        }
        }

        echo "</div>";

        // Donation Box
        ?>
        <div class="spambomb-donation-box">
            <p class="spambomb-error-log-text">SPAMBOMB PLUGIN DONATION</p>
            <p style="color: #fff;">Any Small Donation Will Help Us Maintain The Plugin. Thank You & Enjoy!</p>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
            <input type="hidden" name="cmd" value="_donations" />
            <input type="hidden" name="business" value="max@maxweb.co" />
            <input type="hidden" name="currency_code" value="USD" />
            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
            <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
            </form>
        </div>

        <div class="spambomb-review-box">
        <p class="spambomb-error-log-text">WANT TO LEAVE A REVIEW?</p>
        <a href="https://wordpress.org/plugins/spambomb/#reviews" style="color: #fff;text-decoration: none;" target="_blank">=> CLICK HERE <=</a>
        </div>
        <?php
} // END FUNCTION
}


//Initialize the plugin page, verification & Content

add_action( 'admin_menu', 'spambomb_add_comments_page' );

//Add Settings link to the plugin

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'spambomb_plugin_settings_link' );

function spambomb_plugin_settings_link( $links )
{
$settings_link = '<a href="edit-comments.php?page=spambomb-delete-comments">Settings</a> ';
array_unshift( $links, $settings_link );
return $links;
}

?>