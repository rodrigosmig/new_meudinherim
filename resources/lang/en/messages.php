<?php

return [
    'not_save'          => 'Could not be saved. Try again.',
    'not_delete'        => 'Could not be deleted. Try again.',
    'ajax_title'        => 'Are you sure?',
    'not_found'         => 'No records found',

    'categories' => [
        'select_type'       => 'Select a category type',
        'not_found'         => 'No Categories Found',
        'create'            => 'Category successfully added.',
        'update'            => 'Category successfully updated.',
        'delete'            => 'Category successfully deleted',
        'ajax_text'         => 'The category will be permanently deleted',
    ],

    'accounts' => [
        'not_found'         => 'No Account Found',
        'create'            => 'Account successfully added.',
        'update'            => 'Account successfully updated.',
        'delete'            => 'Account successfully deleted',
        'ajax_text'         => 'The account will be permanently deleted',
    ],

    'cards' => [
        'not_found'         => 'No Credit Cards Found',
        'create'            => 'Credit Card successfully added.',
        'update'            => 'Credit Card successfully updated.',
        'delete'            => 'Credit Card successfully deleted',
        'ajax_text'         => 'The credit card will be permanently deleted',
    ],

    'invoices' => [
        'not_found'         => 'No Invoices Found',
        'create'            => 'Invoices successfully added.',
        'update'            => 'Invoices successfully updated.',
        'delete'            => 'Invoices successfully deleted',
        'ajax_text'         => 'The invoices will be permanently deleted',
    ],

    'entries' => [
        'not_found'             => 'No Entry Found',
        'create'                => 'Entry successfully added.',
        'update'                => 'Entry successfully updated.',
        'delete'                => 'Entry successfully deleted',
        'ajax_text'             => 'The entry will be permanently deleted',
        'no_open_invoice'       => 'There are no open invoices for this date',
        'insufficient_limit'    => 'The card limit is insufficient'
    ],

    'account_scheduling' => [
        'not_found'                 => 'No Account Found',
        'payable_created'           => 'Accounts payable added successfully',
        'receivable_created'        => 'Accounts receivable added successfully',
        'payable_updated'           => 'Accounts payable updated successfully',
        'receivable_updated'        => 'Accounts receivable updated successfully',
        'payable_deleted'           => 'Accounts payable successfully deleted',
        'receivable_deleted'        => 'Accounts receivable successfully deleted',
        'ajax_text'                 => 'When you confirm, the entry that was generated in the account will be permanently deleted',
        'payable_is_paid'           => 'Account payable is already paid',
        'receivable_is_paid'        => 'Account receivable is already paid',
        'payable_cancel'            => 'Payment cancelled successfully',
        'receivable_cancel'         => 'Receivement cancelled successfully',
        'not_cancel_payment'        => 'Payment could not be canceled',
        'not_cancel_receivement'    => 'Receivement could not be canceled',
    ]
];
