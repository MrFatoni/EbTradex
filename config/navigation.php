<?php
return [
    'registered_place' => [
        'back-end','front-end', 'optional',
    ],
    'navigation_template' =>[
        'default_nav' => [
            'navigation_wrapper_start'=> '<ul class="sidebar-menu" data-widget="tree">',
            'navigation_wrapper_end'=> '</ul>',
            'navigation_item_wrapper_start'=> '<li>',
            'navigation_item_wrapper_end'=> '</li>',

            'navigation_item_beginning_wrapper_start'=> '',
            'navigation_item_beginning_wrapper_end'=> '',
            'navigation_item_text_wrapper_start'=> '<span>',
            'navigation_item_text_wrapper_end'=> '</span>',
            'navigation_item_ending_wrapper_start'=> '<span class="pull-right-container">',
            'navigation_item_ending_wrapper_end'=> '</span>',
            'navigation_item_icon_wrapper_start'=> '<i>',
            'navigation_item_icon_wrapper_end'=> '</i>',

            'navigation_sub_menu_wrapper_start'=> '<ul class="treeview-menu">', //submenu binder like <ul>
            'navigation_sub_menu_wrapper_end'=> '</ul>',  // submenu binder like </ul>
//            'navigation_item_wrapper_in_sub_menu_start'=> '<li>',
//            'navigation_item_wrapper_in_sub_menu_end'=> '</li>',

            'navigation_item_icon_position'=> 'text-left',      // top-left / top-right / bottom-left / bottom-right / text-left / text-right / full-right / full-left

            'navigation_item_link_class'=> '',
            'navigation_item_link_active_class'=> 'active',
            'navigation_item_active_class_on_anchor_tag'=> false,   // true/false
            'navigation_item_no_link_text'=> '',                // either # or javascript:;

            'mega_menu_wrapper_start'=>'<div class="megamenu-container">',
            'mega_menu_wrapper_end'=>'</div>',
            'mega_menu_section_wrapper_start'=>'<div class="megamenu-section">',
            'mega_menu_section_wrapper_end'=>'</div>'
        ],
    ],
];