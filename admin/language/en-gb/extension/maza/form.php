<?php
// Heading
$_['heading_title']                 = 'Form';
       
// Text       
$_['text_success']                  = 'Success: You have modified form!';
$_['text_list']                     = 'Form List';
$_['text_add']                      = 'Add Form';
$_['text_edit']                     = 'Edit Form';
$_['text_field']                    = 'Field';
$_['text_admin']                    = 'Admin';
$_['text_general']	                = 'General';
$_['text_data']	                    = 'Data';
$_['text_spam_filter']              = 'Spam filter';
 
// Column 
$_['column_name']                   = 'Name';
$_['column_records']                = 'Records';
$_['column_action']                 = 'Action';
$_['column_date_added']             = 'Date added';
       
// Tab
// $_['tab_form']                       = 'Form';
$_['tab_field']                      = 'Field';
// $_['tab_record']                     = 'Record';

// Entry
$_['entry_name']                    = 'Name';
$_['entry_success']                 = 'Success';
$_['entry_captcha']                 = 'Captcha';
$_['entry_spam_keywords']           = 'Spam keywords';
$_['entry_policy']                  = 'Policy';
$_['entry_record']                  = 'Record';
$_['entry_submit_text']             = 'Submit Text';
$_['entry_email']                   = 'Email';
$_['entry_subject']                 = 'Subject';
$_['entry_message']                 = 'Message';
$_['entry_admin']                   = 'Admin';
$_['entry_customer']                = 'Customers';


// Button
$_['button_record']                 = "View Records";

// Help
$_['help_record']                   = "Save form submit data to database";
$_['help_submit_text']              = "Submit button text";
$_['help_field_email']              = "Select email field of form. Only supported field type email";
$_['help_field_subject']            = "Select field of form for subject. Only supported field type text";
$_['help_mail_admin']               = "Enable to Send alert email to store admin when customer submit form";
$_['help_mail_customer']            = "Enable to Send alert email to customer email id when customer submit form";
$_['help_admin_mailto']             = "Enter email id, on which admin gets alert email on form submit. Stay empty to use store email";
$_['help_success']                  = "Add success message which will display when form successfully submited by customer";
$_['help_spam_keywords']            = 'Add comma separated spam keywords. Mail will be rejected if spam keyword found in mail body.';

// Error
$_['error_warning']                 = 'Warning: Please check the form carefully for errors!';
$_['error_permission']              = 'Warning: You do not have permission to modify form!';
$_['error_name']                    = 'Form Name must be between 1 and 255 characters!';
$_['error_success']                 = 'Success message must be between 1 and 1000 characters!';
$_['error_submit_text']             = 'Form Submit text must be between 1 and 100 characters!';
$_['error_customer_subject']        = 'Form subject must be between 1 and 255 characters!';
$_['error_customer_message']        = 'Message must be available for customer mail!';