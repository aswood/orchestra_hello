<?php
/*
 *	Official English Language File for Clivo v 2.5.1+
 *	@author	Tommy Marshall
 *	@email	tom@sirestudios.com
 *
 *	define('LABEL','Your Translation Here');
 *
 *	Save as "lang.**.inc.php", where ** is your language acronym,
 *	then save your file within the /includes folder.
 */

// Main Navigation
define('DASHBOARD', 'Dashboard');
define('CLIENTS', 'Clients');
define('INVOICES', 'Invoices');
define('PROJECTS', 'Projects');
define('MESSAGES', 'Messages');
define('ADMINS', 'Admins');
define('SETTINGS', 'Settings');

// Side Navigation
define('ADD_CLIENT', 'Add Client');
define('ADD_INVOICE', 'Add Invoice');
define('ADD_ADMIN', 'Add Admin');
define('ADD_PROJECT', 'Add Project');
define('NEW_MESSAGE', 'New Message');

// Dashboard
define('RECENT_TRANS', 'Recent Transactions');
define('INVOICE_STATS', 'Invoices Statistics for Past Three Months');

// Clients
define('COMPANY', 'Company');
define('NAME', 'Name');
define('EMAIL', 'Email');

// Projects
define('PROJECT', 'Project');
define('WORKSPACE', 'Image/File Workspace');
define('DISCUSSION', 'Discussion');
define('ADD_NOTE', 'Add Note');
define('WORKSPACE_DESCRIPTION', 'Click on the images to the right to display them in this viewing<br />pane. Additionally, you can tag images with \'Notes\'.');
define('IN_PROGRESS', 'In Progress');
define('FINISHED', 'Finished');
define('INFORMATION', 'Information');
define('ASSETS', 'Assets');
define('PROJ_TIMELINE', 'Project Timeline');
define('UPLOAD', 'Upload Files');
define('IMG_NOTES', 'Image Notes');
define('STATUS', 'Status');
define('DESCRIPTION', 'Description');
define('EDIT', 'Edit');
define('DELETE_PROJECTS_MSG', 'Below you can choose to delete this project. Additionally, you can choose to delete the project and assets separately.');

// Invoices
define('INV_NUM', 'Inv. #');
define('CLIENT', 'Client');
define('DATE', 'Date');
define('BALANCE', 'Balance');
define('OPTIONS', 'Options');
define('EDIT_INV', 'Edit Invoice');
define('VIEW_PERM', 'View Permalink');
define('REMINDER', 'Send Reminder');
define('TRANS', 'Transactions');
define('DELETE_INV', 'Delete Invoice #%s');
define('DEL_TRANS_MSG', 'Delete all transactions associated with this invoice.');
define('TOTAL_PAID', 'Total Paid');
define('PARTIAL_PAID', 'Partial Paid');
define('NOTHING_PAID', 'Nothing Paid');
define('SYMBOLS', '$ - USD - U.S. Dollar<br />$ - AUD - Australian Dollar<br />&euro; - EUR - Euros<br />&yen; - JPY - Japanese Yen<br />&pound; - GBP - British Pound<br />$ - CAD - Canadian Dollar<br />&#8355; - CHF - Swiss Franc<br />$ - MXN - Mexican Peso<br />z&#322; - PLN - Polish Zloty<br />');
define('KEY', 'Key');
define('PRIVATE_MSG', 'Private Message');
define('BOTH', 'Both');

// Messages
define('REPLY', 'Reply Now');
define('FROM', 'From');
define('SUBJECT', 'Subject');
define('MESSAGE', 'Message');
define('SENT_MESSAGES', 'Sent Messages');
define('TO', 'To');
define('YOU','You'); // 'You', as in, "You sent this"

// Admin
define('ADD_AN_ADMIN', 'Add an Admin');
define('ADD_ADMIN', 'Add Admin');
define('EDIT_ADMIN', 'Edit Admin');

// General Form
define('FULL_NAME', 'First &amp; Last Name');
define('LOGIN', 'Login');
define('PASS', 'Password');
define('ADDRESS', 'Address');
define('CITY', 'City');
define('STATE', 'State');
define('ZIP', 'Zip Code');
define('STATE', 'State');

// Transactions
define('ADD_TRANS', 'Add Transaction');
define('METHOD', 'Method');
define('AMOUNT', 'Amount');
define('RECENT_TRANS_TO', 'Recent Transactions to Invoice #%s');

// Settings
define('GLOBAL_SETTINGS', 'Global Settings');
define('PAYMENT_HEADER', 'Payment Notifications');
define('PAYMENT_MSG', 'Selecting \'Yes\' below will send an email thank you receipt to the specified email address each time a payment is processed.');
define('PAYMENT_EMAIL', 'Payment Email');
define('PAYMENT_EMAIL_MSG', 'Select the message subject and message that will be sent each time a payment is processed.');
define('INVOICE_HEADER', 'Invoice Message Details');
define('INVOICE_HEADER_MSG', 'Choose a message subject and message to be sent each time an invoice is created.');
define('PAYPAL', 'Paypal');
define('AUTHORIZE', 'Authorize.NET');
define('INVOICE_VIEWING', 'Invoice Viewing');
define('INVOICE_VIEWING_MSG', 'This text will display on each invoice page above the payment options.');
define('WELCOME_EMAIL', 'Welcome Email');
define('WELCOME_MSG', 'Select the message subject and message that will be sent to the client upon account creation.');
define('PROJECT_EMAIL', 'Project Creation Email');
define('PROJECT_MSG', 'Select the message subject and message that will be sent to the client upon project creation.');
define('MISC', 'Miscellaneous');
define('MISC_MSG', 'Change the invoice headers, message signature and default language.');
define('ACCEPTED_HEADER', 'Accepted Payment Methods');
define('ACCEPTED_MSG', 'Options available to the client when paying an invoice. Edit the includes/config.inc.php to change additional details.');
define('INVOICE_LOGO', 'Invoice Logo URL');
define('HEADER_IMG', 'Header Logo Image');
define('PRE_PAYMENT_MSG', 'Pre-Payment Message');
define('NEW_CLIENT_HEADER', 'New Client Details');
define('NEW_CLIENT_DESCRIPTION', 'Each time a new client is created, the following operations will committed.');
define('SEND_MSG', 'Send Message');
define('SYS_CHANGED_SUCCESS', 'Your settings have been successfully changed. To see the effects you may have to refresh your page or click on any of the links above.');
define('WELCOME_MSG', 'Welcome Message');
define('LANGUAGE', 'Default Language');
define('MY_LANGUAGE', 'My Language');
define('CHOOSE_THEME', 'Choose a Theme');
define('PERSONALIZE', 'Personalize');
define('PERSONALIZE_MSG', 'Below you can change your theme and default language upon signing in.');
define('SIGNATURE', 'Signature');
define('ADMIN', 'Admin');
define('NOTES', 'Notes');
define('NOTES_MSG', 'Enabling Authorize.NET will force SSL when viewing an invoice. This ensures payment information is secure and follows Authorize.NET\'s guidelines.');

// Languages
define('ENGLISH', 'English');
define('SPANISH', 'Spanish');
define('GERMAN', 'German');
define('DUTCH', 'Dutch');
define('POLISH', 'Polish');
define('SWEDISH', 'Swedish');

// Add Client
define('CONTACT_INFO', 'Contact Information');
define('CLIENT_PANEL', 'Client Panel Login');
define('CLIENT_PANEL_MSG', 'The credentials below will be used by the client to login and view their account details.');
define('ADD_CLIENT', 'Add Client');
define('CLIENT_ADDED','To add another client <a href="addclient.php">click here</a> or to view and make additional changes to  %s\'s details, <a href="clients.php?id=%s">click here</a>');

// Add Invoice
define('INVOICE', 'Invoice');
define('PDF_INVOICE', 'PDF Invoice');
define('DATE', 'Date');
define('STATUS', 'STATUS');
define('COST', 'Total Cost');
define('SEND_NOTIFICATION', 'Send Notification');
define('CREATE_INVOICE', 'Create another invoice');
define('CLIENT_REVIEW', 'Review %s\'s details');
define('CLIENT_INVOICES', 'View invoices for %s');
define('CREATE_INVOICE', 'Create another invoice');
define('INVOICE_ADDED', 'Invoice successfully added!');
define('INVOICE_ALRDY', 'Invoice already created!');
define('INVOICE_ALRDY_MSG', 'That invoice has already been created. Please <a href="addinvoice.php">click here</a> to create another invoice.');

// Add Project
define('PROJECT_ALRDY', 'Project already created!');
define('PROJECT_ALRDY_MSG', 'That project has already been created. Please <a href="addproject.php">click here</a> to create another project.');
define('VIEW_PROJECTS', 'View all projects');
define('VIEW_PROJECT_PAGE', 'View %s\'s project page');
define('CREATE_PROJECT', 'Create another project');
define('PROJECT_ADDED', 'Project successfully added!');
define('NOW_CAN_DO', 'Now you can do the following:');

// Add Client
define('CLIENT_ALRDY', 'You must have tried to refresh the page since %s already exists in our database. If you would like to add another client <a href="addclient.php">click here</a>.');

// Pagination
define('FIRST', 'FIRST');
define('PREVIOUS', 'PREVIOUS');
define('NEXT', 'NEXT');
define('LAST', 'LAST');
define('PAGE', 'Page');
define('OF', 'of');

// General Forms/Buttons
define('YES', 'Yes');
define('NO', 'No');
define('SUBMIT', 'Submit');
define('APPLY', 'Apply');
define('DELETE', 'Delete');
define('CANCEL', 'Cancel');

// Client
define('OUTSTANDING', 'Outstanding');
define('NEW_INVOICE', 'New Invoice');
define('NEW_PROJECT', 'New Project');

// Edit Client
define('BILLING', 'Additional/Billing Information');
define('BILLING_MSG', 'If you fill in the fields below they will automatically be loaded into the appropriate fields each time you view an invoice, making the payment process faster.');
define('DELETE_CLIENT_MSG', 'Below you can choose to delete this client. Additionally, you can choose to delete the client, invoices and transactions separately.');
define('SUCCESS_DELETE', 'successfully deleted');
define('FAIL_DELETE', 'unsuccessfully deleted');
define('UNSUCCESS_MOD', 'unsuccessfully modified');
define('SUCCESS_MOD', 'successfully modified');

// Edit Invoice
define('MARK_AS', 'Mark as');
define('UNPAID', 'Unpaid');
define('LEAVE', 'Leave as is');

// Misc
define('PAID', 'paid');
define('WITH', 'with');
define('ON', 'on');
define('ORR', 'or');
define('VIEW', 'View');
define('GREETING', 'Logged in as <span>%s</span> | <a href="%s"><span>Logout</span></a>');
define('EDIT', 'Edit');
define('CHARGED', 'Charged');
define('RECEIVED', 'Received');
define('LOGIN_URL', 'Website Login');
define('OPEN', 'Open');
define('NONE', 'None');
define('START', 'Start');
define('END', 'End');

// View
define('BALANCE', 'Balance');
define('VIEW_INVOICE', 'View Invoice');
define('FIRST_NAME', 'First Name');
define('LAST_NAME', 'Last Name');
define('PHONE', 'Phone');
define('CC', 'Credit Card');
define('CC_NUM', 'Credit Card Number');
define('CC_CVV', 'CVV');
define('CC_EXP', 'Exp. Date');
define('AVAILABLE', 'Available:');
define('PAY', 'Pay Balance with');
define('APPLY_ALL', 'Apply all remaining ');
define('CUSTOM_AMOUNT', 'Enter custom amount');
define('INVOICE_HISTORY', 'Invoice History');
define('CREDITS_REMAINING', 'It looks like you have no %s remaining. If you think this is a mistake, please contact our administrator by <a href="mailto:%s">clicking here</a>.');
define('INVOICE_PAID', 'This invoice has already been paid. Thank you!');

// Process
define('PROCESSING', 'Processing Payment...');
define('PAY_MSG', 'If you have a Paypal account, you may wish to pay using that. A PayPal account is not required to make a secure payment.');
define('PAYMENT_DECLINED', 'Payment Declined!');
define('PAYMENT_DECLINED_MSG', 'Your payment was declined for the following reason:<br /><strong>%s</strong>. <br /><br />Please <a href="%s">click here</a> to try again. If the error persists, try using another payment method or contact our administrator by <a href="mailto:%s">clicking here</a>.');
define('PAYMENT_ERROR', 'Payment Error!!');
define('PAYMENT_ERROR_MSG', 'Your payment was in error for the following reason:<br /><strong>%s</strong>. <br /><br />Please <a href="%s">click here</a> to try again. If the error persists, try using another payment method or contact our administrator by <a href="mailto:%s">clicking here</a>.');
define('ERROR_PROCESSING', 'There was an error processing your request.');
define('ERROR_PROCESSING_MSG', 'Please <a href="%s">click here</a> to try again. If the error persists, try using another payment method or contact us our administrating by <a href="mailto:%s">clicking here</a>. We apologize for the inconvenience.');
define('REMAINING_CREDITS', 'Total <em>%s</em> remaining:');
define('PAY_ERROR', 'Payment Error!');
define('PAY_RESPONSE', 'Your payment was in error for the following reason:');
define('PAY_ERROR_MSG', 'Please <a href="%s">click here</a> to try again. If the error persists, try using another payment method or contact us at <a href="mailto:%s">%s</a>.');
define('PAY_SUCCESS', 'Payment Accepted!');
define('PAY_SUCCESS_MSG', 'Thank you for your payment of %s. To view your invoice history, please <a href="%s">click here</a>.');
define('CREDIT_SUCESS_MSG', 'You still have a balance of %s on this invoice. To make another payment, <a href="%s">click here</a>.');
define('CREDIT_ERROR', 'Not enough credits. Please use another payment option. <a href="%s">Click here</a> to go back.');

// Login
define('ADMIN_LOGIN', 'Admin Login');
define('CLIENT_LOGIN', 'Client Login');
define('INCORRECT_UP', 'Invalid Login/Password Combination.');
define('REMEMBER_ME', 'Remember me?');
define('DENIED', 'Access Denied');
define('DENIED_MSG', 'You are trying to access a part of this website which you do not have access to. Please <a href="?logout=1">logout</a> and try again.');

// System Messages
define('SYS_SUCCESS', 'Success!');
define('SYS_SUCCESS_MSG', 'Changes have been successfully made.');
define('SYS_ERROR', 'Error!');
define('SYS_ERROR_MSG', 'Changes have not been successfully made.');
define('CLOSE', 'close');
define('SYS_INCOMPLETE', 'Incomplete submission');
define('SYS_INCOMPLETE_MSG', 'Some of the fields were not filled out correctly. Please double check and try again.');

// Compose
define('MSG_RE','Re:');
define('ORIGINAL_MSG', 'ORIGINAL MESSAGE');
define('RECIPIENT', 'Recipient');

// Reminder, Compose
define('DEAR', 'Dear');
define('REMINDER_BODY', 'Dear {CLIENT},

This is a reminder that you have an unpaid balance of {BALANCE} to an invoice. To pay the remaining balance, please click on the following link: 
{LINK}

{SIGNATURE}');
define('REMINDER_SUB', 'Reminder Regarding Unpaid Balance');
define('MSG_SENT', 'Message Sent!');
define('MSG_MSG', 'Message has been successfully sent. Click on the \'Close\' link to the bottom right to close this window.');
define('MSG_FAIL', 'There seems to have been an error sending the message. Please check you have the PHP mail() enabled and try again. If the problem persists, contact <a href="mailto:clivo@sirestudios.com">clivo@sirestudios.com</a>. Click on the \'Close\' link to the bottom right to close this window.');

// Client 
define('EDIT_ACCOUNT', 'Edit Account');
define('MY_ACCOUNT', 'My Account');
define('USING', 'using');
define('MAKE_PAYMENT', 'Make Payment');
define('CLIENT_PANEL', '%s Client Panel'); // %s is Clients Full Name
define('LOGOUT', 'Logout');

?>