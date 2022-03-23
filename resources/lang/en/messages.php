<?php

return [
    'not_save'              => 'Could not be saved. Try again.',
    'not_delete'            => 'Could not be deleted. Try again.',
    'ajax_title'            => 'Are you sure?',
    'not_found'             => 'No records found',
    'parcel_text'           => 'Do you wish delete all installments?',

    'categories' => [
        'select_type'       => 'Select a category type',
        'not_found'         => 'No Categories Found',
        'create'            => 'Category successfully added.',
        'update'            => 'Category successfully updated.',
        'delete'            => 'Category successfully deleted',
        'ajax_text'         => 'The category will be permanently deleted',
        'invalid_type'      => 'The category type is invalid',
        'api_not_found'     => 'Category Not Found',
        'not_delete'        => 'The category is associated with entries, you cannot delete'
    ],

    'accounts' => [
        'not_found'             => 'No Account Found',
        'create'                => 'Account successfully added.',
        'update'                => 'Account successfully updated.',
        'delete'                => 'Account successfully deleted',
        'ajax_text'             => 'The account will be permanently deleted',
        'equal_accounts'        => 'Source and destination accounts cannot be the same',
        'transfer_completed'    => 'Transfer completed successfully',
        'api_not_found'         => 'Account Not Found',
        'not_delete'            => 'The card has entries, you cannot delete'
    ],

    'cards' => [
        'not_found'         => 'No Credit Cards Found',
        'create'            => 'Credit Card successfully added.',
        'update'            => 'Credit Card successfully updated.',
        'delete'            => 'Credit Card successfully deleted',
        'ajax_text'         => 'The credit card will be permanently deleted',
        'api_not_found'     => 'Card Not Found',
        'not_delete'        => 'The card has invoices, you cannot delete'
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
        'insufficient_limit'    => 'The card limit is insufficient',
        'invalid_card'          => 'The informed card was not found',
        'invalid_invoice'       => 'The informed invoice was not found',
        'api_not_found'         => 'Entry Not Found',
        'delete_parcel'         => 'When confirming, all installments will be excluded',
        'not_parcel'            => 'Este lançamento não possui parcelas',
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
        'receivable_is_paid'        => 'Account receivable is already paid',
        'payable_paid'              => 'Account successfully paid',
        'receivable_paid'           => 'Account received successfully',
        'payable_cancel'            => 'Payment cancelled successfully',
        'receivable_cancel'         => 'Receivement cancelled successfully',
        'not_cancel_payment'        => 'Payment could not be canceled',
        'not_cancel_receivement'    => 'Receivement could not be canceled',
        'installments_created'      => 'Installments successfully created',
        'api_not_found'             => 'Account not found',
        'delete_payable_paid'       => 'It is not possible to delete a paid account. Cancel the payment',
        'delete_receivable_paid'    => 'It is not possible to delete a received account. Cancel the receivement',
        'update_payable_paid'       => 'It is not possible to update a paid account. Cancel the payment',
        'update_receivable_paid'    => 'It is not possible to update a received account. Cancel the receivement',
        'account_is_paid'           => 'It is not possible to process the request because the account is paid. Try canceling payment/receivement for this account',
        'account_is_not_paid'       => 'It is not possible to process the request because the account is not paid',
        'not_payable'               => 'This account is not an Accounts Payable',
        'not_receivable'            => 'This account is not an Accounts Receivable'
    ],

    'profile' => [
        'incorrect_password'    => 'Your current password is incorrect.',
        'same_password'         => 'The current password and the new password cannot be the same',
        'password_updated'      => 'Password updated successfully',
        'profile_updated'       => 'Profile updated successfully',
        'invalid_user'          => 'Invalid User',
        'avatar_updated'        => 'Avatar updated successfully',
    ],

    'mail' => [
        'payable_due_in'    => "Accounts payable due in",
        'receivable_due_in' => "Accounts receivable due in",
        'footer_mail'       => 'If you’re having trouble clicking the :actionText button, copy and paste the URL below into your web browser:'
    ],

    'parcels' => [
        'not_found'     => 'Installment Not Found',
        'select_parcel' => 'Select at least one installment',
        'anticipate'    => 'Anticipated installments successfully',
        'parcel_number' => 'The parcel number is required'
    ],

    'recaptcha' => [
        'invalid_token' => 'The reCaptcha token is invalid. You are not human!'
    ]
];
