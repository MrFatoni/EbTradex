<?php
return [
    'fixed_roles' => [USER_ROLE_SUPER_ADMIN, USER_ROLE_USER, USER_ROLE_TRADE_ANALYST],

    'fixed_users' => [FIXED_USER_SUPER_ADMIN],

    'front_end_view' => [
        'filter' => 'frontend.renderable_template.filters',
        'pagination' => 'frontend.renderable_template.pagination'
    ],
    'back_end_view' => [
        'filter' => 'backend.renderable_template.filters',
        'pagination' => 'backend.renderable_template.pagination'
    ],
    'path_profile_image' => 'images/users/',

    'path_image' => 'images/',
    'path_id_image' => 'images/id/',

    'path_stock_item_emoji' => 'images/stock-emoji/',
    'path_post' => 'images/posts/',

    'email_status' => [
        EMAIL_VERIFICATION_STATUS_ACTIVE => ['color_class' => 'success'],
        EMAIL_VERIFICATION_STATUS_INACTIVE => ['color_class' => 'danger'],
    ],
    'account_status' => [
        ACCOUNT_STATUS_ACTIVE => ['color_class' => 'success'],
        ACCOUNT_STATUS_INACTIVE => ['color_class' => 'warning'],
        ACCOUNT_STATUS_DELETED => ['color_class' => 'danger'],
    ],
    'payment_status' => [
        PAYMENT_REVIEWING => ['color_class' => 'warning'],
        PAYMENT_PENDING => ['color_class' => 'info'],
        PAYMENT_COMPLETED => ['color_class' => 'success'],
        PAYMENT_FAILED => ['color_class' => 'danger'],
        PAYMENT_DECLINED => ['color_class' => 'danger'],
    ],
    'id_status' => [
        ID_STATUS_VERIFIED => ['color_class' => 'danger'],
        ID_STATUS_PENDING => ['color_class' => 'warning'],
        ID_STATUS_VERIFIED => ['color_class' => 'success'],
    ],
    'financial_status' => [
        FINANCIAL_STATUS_ACTIVE => ['color_class' => 'success'],
        FINANCIAL_STATUS_INACTIVE => ['color_class' => 'danger'],
    ],
    'maintenance_accessible_status' => [
        UNDER_MAINTENANCE_ACCESS_ACTIVE => ['color_class' => 'success'],
        UNDER_MAINTENANCE_ACCESS_INACTIVE => ['color_class' => 'danger'],
    ],
    'image_extensions' => ['png', 'jpg', 'jpeg', 'gif'],
    'system_notice_types' => ['warning', 'success', 'danger', 'primary', 'info'],
    'currency_transferable' => [CURRENCY_REAL, CURRENCY_CRYPTO],
    'strip_tags' => [
        'escape_text' => ['beginning_text', 'ending_text', 'content'],
        'escape_full_text' => [],
        'allowed_tag_for_escape_text' => '<a><img><p><br><u><strong><em><ul><ol><li><i>',
        'allowed_tag_for_escape_full_text' => '<i><a><img><h1><h2><h3><h4><h5><h6><hr><article><section><video><audio><table><tr><td><thead><tfoot><footer><header><p><br><u><strong><em><ul><ol><dl><dt><li><div><sub><sup><span>',
    ],

    'available_commands' => [
        'cache' => 'cache:clear',
        'config' => 'config:clear',
        'route' => 'route:clear',
        'view' => 'view:clear',
    ],
    'category_slug' => [
        'exchange' => CATEGORY_EXCHANGE,
//        'margin' => CATEGORY_MARGIN,
//        'lending' => CATEGORY_LENDING,
        'ico' => CATEGORY_ICO,
    ],

    'journal_type' => [
        'from-wallet-on-order' => DECREASED_FROM_WALLET_ON_ORDER_PLACE,
        'to-order-on-order' => INCREASED_TO_ORDER_ON_ORDER_PLACE,

        'from-order-on-order-cancel' => DECREASED_FROM_ORDER_ON_ORDER_CANCELLATION,
        'to-wallet-on-order-cancel' => INCREASED_TO_WALLET_ON_ORDER_CANCELLATION,

        'from-order-on-settlement' => DECREASED_FROM_ORDER_ON_SETTLEMENT,
        'to-wallet-on-settlement' => INCREASED_TO_WALLET_ON_SETTLEMENT,


        'from-order-on-transaction' => DECREASED_FROM_ORDER_ON_SUCCESSFUL_TRANSACTION,
        'to-exchange-on-transaction' => INCREASED_TO_EXCHANGE_ON_SUCCESSFUL_TRANSACTION,
        'from-exchange-on-transaction' => DECREASED_FROM_EXCHANGE_ON_SUCCESSFUL_TRANSACTION,
        'to-wallet-on-transaction' => INCREASED_TO_WALLET_ON_SUCCESSFUL_TRANSACTION,
        'from-exchange-as-exchange-fee' => DECREASED_FROM_EXCHANGE_AS_SERVICE_FEE_ON_SUCCESSFUL_TRANSACTION,
        'to-system-as-exchange-fee' => INCREASED_TO_SYSTEM_AS_SERVICE_FEE_ON_SUCCESSFUL_TRANSACTION,


        'from-wallet-on-withdrawal' => DECREASED_FROM_WALLET_ON_WITHDRAWAL_REQUEST,
        'to-withdrawal-on-withdrawal' => INCREASED_TO_WITHDRAWAL_ON_WITHDRAWAL_REQUEST,

        'from-withdrawal-on-withdrawal-confirm' => DECREASED_FROM_WITHDRAWAL_ON_WITHDRAWAL_CONFIRMATION,
        'to-outside-on-withdrawal-confirm' => INCREASED_TO_OUTSIDE_ON_WITHDRAWAL_CONFIRMATION,

        'from-withdrawal-as-withdrawal-fee' => DECREASED_FROM_WITHDRAWAL_AS_WITHDRAWAL_FEE_ON_WITHDRAWAL_CONFIRMATION,
        'to-system-as-withdrawal-fee' => INCREASED_TO_SYSTEM_ON_AS_WITHDRAWAL_FEE_WITHDRAWAL_CONFIRMATION,

        'from-withdrawal-on-withdrawal-cancel' => DECREASED_FROM_WITHDRAWAL_ON_WITHDRAWAL_CANCELLATION,
        'to-wallet-on-withdrawal-cancel' => INCREASED_TO_WALLET_ON_WITHDRAWAL_CANCELLATION,


        'from-outside-on-deposit' => DECREASED_FROM_OUTSIDE_ON_DEPOSIT_REQUEST,
        'to-deposit-on-deposit' => INCREASED_TO_DEPOSIT_ON_DEPOSIT_REQUEST,

        'from-deposit-on-deposit-confirm' => DECREASED_FROM_DEPOSIT_ON_DEPOSIT_CONFIRMATION,
        'to-wallet-on-deposit-confirm' => INCREASED_TO_WALLET_ON_DEPOSIT_CONFIRMATION,

        'from-deposit-as-deposit-fee' => DECREASED_FROM_DEPOSIT_AS_DEPOSIT_FEE_ON_DEPOSIT_CONFIRMATION,
        'to-system-as-deposit-fee' => INCREASED_TO_SYSTEM_AS_DEPOSIT_FEE_DEPOSIT_CONFIRMATION,

        'from-deposit-on-deposit-cancel' => DECREASED_FROM_DEPOSIT_ON_DEPOSIT_CANCELLATION,
        'to-outside-on-deposit-cancel' => INCREASED_TO_OUTSIDE_ON_DEPOSIT_CANCELLATION,
        'to-system-on-transaction' => INCREASED_TO_SYSTEM_ON_SUCCESSFUL_TRANSACTION,

        'from-wallet-on-ico' => DECREASED_FROM_SYSTEM_ON_ICO_SALE,
        'to-wallet-on-ico' => INCREASED_TO_WALLET_FROM_SYSTEM_ON_ICO_PURCHASE,

        'from-exchange-as-referral_earning' => DECREASED_FROM_EXCHANGE_REFERRAL_EARNING_ON_SUCCESSFUL_TRANSACTION,
        'to-wallet-as-referral_earning' => INCREASED_TO_WALLET_AS_REFERRAL_EARNING_ON_SUCCESSFUL_TRANSACTION,
    ],
];