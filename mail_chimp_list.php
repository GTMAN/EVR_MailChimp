<?php

#Get API and List ID

add_action('evr_process_confirmation','evr_mailchimp');

require_once('inc/MailChimp.class.php');
 
function evr_mailchimp($reg_form){

global $wpdb;
$evrMCoptions = get_option('evr-mailchimp-option');
$api= $evrMCoptions['api_number'];
$list=$evrMCoptions['list_id'];
$grouping=$evrMCoptions['grouping_name'];


# Add GROUPINGS Name to the list. If groups are not enabled they will automatically be switched on.
$MailChimp = new MailChimp($api);
$resultgr = $MailChimp->call ( 'lists/interest-grouping-add' , array(
					'id'			=> $list,
					'name'	  		=> $grouping,
					'type'			=> 'hidden',
					'groups'		=>	array($reg_form['event_name']),

			));
			//print_r ($resultgr);


#Add interest group name to list. Name is event_name passed in from Event Registration.	 		
$MailChimp = new MailChimp($api);
$resultg = $MailChimp->call ( 'lists/interest-group-add' , array(
					'id'			=> $list,
					'group_name'	=> $reg_form['event_name'],				

			));
			//print_r ($resultg);


#	Subscribes person to the list and adds the event_name as an interest group.
$result = $MailChimp->call('lists/subscribe', array(
                'id'                => $list,
                'email'             => array('email'=>$reg_form['email']),
                'merge_vars'        => array('FNAME'=>$reg_form['fname'], 'LNAME'=>$reg_form['lname'],'MMERGE3'=>$reg_form['phone'],
											'GROUPINGS'=>array(array('name'=> $grouping, 'groups'=> array($reg_form['event_name'])))),
                'double_optin'      => false,
                'update_existing'   => true,
                'replace_interests' => true,
                'send_welcome'      => false,
				
            ));
//print_r($result);
 
}

?>