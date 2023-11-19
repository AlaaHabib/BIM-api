<?php

namespace App\Constants;

class TransactionConstants
{
    const TRANSACTION_1001 = "TRANSACTION-1001";
    const TRANSACTION_1002 = "TRANSACTION-1002";
    const TRANSACTION_1003 = "TRANSACTION-1003";
    const TRANSACTION_1004 = "TRANSACTION-1004";
    const TRANSACTION_1005 = "TRANSACTION-1005";

    const PAYMENT_2001 = "PAYMENT-2001";
    const PAYMENT_2002 = "PAYMENT-2002";

    const AUTHOR_3001 = "AUTHOR-3001";

    const AUTH_4001 = "AUTH-4001";
    const AUTH_4002 = "AUTH-4002";
    const AUTH_4003 = "AUTH-4003";




    const RESPONSE_CODES_MESSAGES = [
        self::TRANSACTION_1001 => 'translation.listRetrieved',
        self::TRANSACTION_1002 => 'translation.listUpdated',
        self::TRANSACTION_1003 => 'translation.createdSuccefully',
        self::TRANSACTION_1004 => 'translation.notFound',
        self::TRANSACTION_1005 => 'translation.deleted',

        self::PAYMENT_2001 => 'translation.paymentCreatedSuccefully',
        self::PAYMENT_2002 => 'translation.listRetrieved',

        self::AUTHOR_3001 => 'translation.unauthorizated',

        self::AUTH_4001 => 'translation.authenticated',
        self::AUTH_4002 => 'translation.unauthenticated',
        self::AUTH_4003 => 'translation.logout',
    ];
}
