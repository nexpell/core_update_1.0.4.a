<?php
return [
    'step_title' => 'Step 5: Create Admin Account',
    'section_admin_account' => 'Create Administrator Account',
    'intro_text' => 'Create your personal administrator account, which will be the main access to your new CMS. This account has full access to all functions, including managing content, users, and system configurations. Choose a strong username and a secure password to protect your system. After the installation is complete, you will log in to the admin area of your CMS with these credentials.',
    'field_username' => 'Username:',
    'field_email' => 'Email:',
    'field_password' => 'Password:',
    'field_weburl' => 'Web URL:',
    'button_create_admin' => 'Create Admin Account',
    'msg_error_fields_empty' => '❌ Please fill in all fields.',
    
    // Status and Error Messages
    'msg_error_config_missing' => '❌ Configuration file is missing. Please complete Step 2 and 3 first.',
    'msg_error_db_connect' => '❌ Database connection error: ',
    'msg_error_sql_not_found' => '❌ Admin SQL file not found: ',
    'msg_error_exec_sql' => '❌ Error executing SQL commands: ',
    'msg_error_partial_sql' => '⚠️ Error in a SQL part: ',
    'msg_admin_exists' => 'ℹ️ Admin user already exists. You can <a href="?reset=1" class="btn btn-sm btn-outline-danger">reset here</a>.',
    'msg_success' => '✅ Admin account successfully created! You will be automatically redirected to <strong>Step 6</strong> in a few seconds.',
    'msg_redirecting' => 'You will be automatically redirected in a few seconds.',

    // Debug messages (optional, for developers)
    'debug_reset_start' => '🧹 Debug: Removing admin database entries...',
    'debug_reset_complete' => '✅ Debug: Reset complete.',
];