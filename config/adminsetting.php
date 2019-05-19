<?php
return [
    'admin_setting_storage' => env('ADMIN_SETTING_STORAGE', 'cache'), // database / cache

//    'admin_settings' => [],

    /*
            text, image, placeholder/textArea, placeholder/select/checkbox, placeholder/radio,
            placeholder
            'field_type'=>'select',
            'input_class'=>'',
            'type_database' => '',
            'type_function' => true,
            'data_array' => 'language',//function name or array
            'slug_text' => 'Language',
            'section_start_tag'=>'',
            'section_end_tag'=>'',
            'slug_start_tag'=>'',
            'slug_end_tag'=>'',
            'value_start_tag'=>'',
            'value_end_tag'=>'',
            'input_start_tag'=>'',
            'input_end_tag'=>'',
            'min' =>0,
            'max' =>10,
            data_type => integer | numeric | email | digit | boolean | required | image
     */
    'settings' => [
        'general' => [
            'lang' => [
                'field_type' => 'select',
                'type_function' => true,
                'data_array' => 'language',
                'slug_text' => 'Language',
            ],
            'maintenance_mode' => [
                'field_type' => 'toggle',
                'type_function' => true,
                'data_array' => 'maintenance_status',
                'slug_text' => 'Maintenance mode',
            ],
            'registration_active_status' => [
                'field_type' => 'toggle',
                'type_function' => true,
                'data_array' => 'active_status',
                'slug_text' => 'Allow Registration',
            ],
            'default_role_to_register' => [
                'field_type' => 'select',
                'type_database' => true,
                'data_array' => ['App\Repositories\Core\Interfaces\UserRoleManagementInterface', 'getUserRoles'],
                'slug_text' => 'Default registration role',
            ],
            'signupable_user_roles' => [
                'field_type' => 'checkbox',
                'previous' => 'default_role_to_register',
                'slug_text' => 'Allowed role for signup',
            ],
            'require_email_verification' => [
                'field_type' => 'toggle',
                'type_function' => true,
                'data_array' => 'active_status',
                'slug_text' => 'Require Email Verification',
            ],
            'display_google_captcha' => [
                'field_type' => 'toggle',
                'type_function' => true,
                'data_array' => 'active_status',
                'slug_text' => 'Google Captcha Protection',
            ],
            'admin_receive_email' => [
                'field_type' => 'text',
                'data_type' => 'email',
                'slug_text' => 'Email to receive customer feedback',
            ],
            'company_name' => [
                'field_type' => 'text',
                'data_type' => 'required',
                'slug_text' => 'Company Name',
            ],
            'company_logo' => [
                'field_type' => 'image',
                'data_type' => 'image',
                'slug_text' => 'Company Logo',
            ],
            'facebook_link' => [
                'field_type' => 'text',
                'slug_text' => 'Facebook Link',
            ],
            'twitter_link' => [
                'field_type' => 'text',
                'slug_text' => 'Twitter Link',
            ],
            'linkedin_link' => [
                'field_type' => 'text',
                'slug_text' => 'Linkedin Link',
            ],
            'google_plus_link' => [
                'field_type' => 'text',
                'slug_text' => 'Google Plus Link',
            ],
            'pinterest_link' => [
                'field_type' => 'text',
                'slug_text' => 'Pinterest Link',
            ]

        ],
        'exchange_setting' => [
            'exchange_maker_fee' => [
                'field_type' => 'text',
                'data_type' => 'numeric',
                'max' => '100',
                'min' => '0',
                'slug_text' => 'Exchange Maker Fee',
            ],
            'exchange_taker_fee' => [
                'field_type' => 'text',
                'data_type' => 'numeric',
                'max' => '100',
                'min' => '0',
                'slug_text' => 'Exchange Taker Fee',
            ],
        ],
        'withdrawal_setting' => [
            'auto_withdrawal_process' => [
                'field_type' => 'toggle',
                'type_function' => true,
                'data_array' => 'active_status',
                'slug_text' => 'Auto Withdrawal Process',
            ],
        ],
        'referral_setting' => [
            'referral' => [
                'field_type' => 'toggle',
                'type_function' => true,
                'data_array' => 'active_status',
                'slug_text' => 'Referral',
            ],
            'referral_percentage' => [
                'field_type' => 'text',
                'data_type' => 'numeric',
                'max' => '100',
                'min' => '0',
                'slug_text' => 'Referral Percentage',
            ],
        ],
    ],


    /*
     * ----------------------------------------
     * ----------------------------------------
     * ALL WRAPPER HERE
     * ----------------------------------------
     * ----------------------------------------
    */
    'common_wrapper' => [
        'section_start_tag' => '<tr>',
        'section_end_tag' => '</tr>',
        'slug_start_tag' => '<td>',
        'slug_end_tag' => '</td>',
        'value_start_tag' => '<td>',
        'value_end_tag' => '</td>',
    ],
    'common_text_input_wrapper' => [
        'input_start_tag' => '<div>',
        'input_end_tag' => '</div>',
        'input_class' => 'form-control',
    ],
    'common_image_input_wrapper' => [
        'input_start_tag' => '<div>',
        'input_end_tag' => '</div>',
        'input_class' => '',
    ],
    'common_textarea_input_wrapper' => [
        'input_start_tag' => '<div>',
        'input_end_tag' => '</div>',
        'input_class' => 'form-control',
    ],
    'common_select_input_wrapper' => [
        'input_start_tag' => '<div>',
        'input_end_tag' => '</div>',
        'input_class' => 'form-control',
    ],
    'common_checkbox_input_wrapper' => [
        'input_start_tag' => '<div class="setting-checkbox">',
        'input_end_tag' => '</div>',
//        'input_class'=>'setting-checkbox',
    ],
    'common_radio_input_wrapper' => [
        'input_start_tag' => '<div class="setting-checkbox">',
        'input_end_tag' => '</div>',
        'input_class' => 'setting-radio',
    ],
    'common_toggle_input_wrapper' => [
        'input_start_tag' => '<div class="text-right">',
        'input_end_tag' => '</div>',
//        'input_class'=>'setting-checkbox',
    ],
];