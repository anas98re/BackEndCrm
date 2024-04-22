<?php


namespace App;

class  Constants
{
    const MAIDANI = 'ميداني';
    const NOTICE_OF_TRANSFERRING_MARKETING_CLIENTS_TO_MY_FIELD_ID = 179;    
    const ALL_BRUNSHES = 14;
    const MARKETING_SALSE_ID = 18;
    const NEGOTIATION = 'تفاوض';
    const OFFER_PRICE = 'عرض سعر';

    //Tickets
    const TICKET_OPEN = 1;
    const TICKET_REOPEN = 6;
    const TICKET_RECIVE = 2;
    const TICKET_CLOSE = 3;
    const TICKET_SUSPEND = 4;
    const TICKET_RATE = 5;


    const PRIVILEGES_IDS = [

        "TRANSFER_CLIENTS_ADMIN" => 183,

        "TRANSFER_CLIENTS_EMPLOYEE" => 184,

        "TRANSFER_CLIENTS_ALL" => 185,

    ];

    const TYPE_ADMINISTRATION = [

        "HIGHER_MANAGEMENT" => 1,
        "SALES_MANAGEMENT" => 2,
        "SUPPORT_MANAGEMENT" => 3,
        "CUSTOMER_CARE_MANAGEMENT" => 4,
        "FINANCIAL_MANAGEMENT" => 5,
        "COLLECTION_MANAGEMENT" => 6,
        "OPERATION_MANAGEMENT" => 7,
        "PROGRAMMING_MANAGEMENT" => 8,
        "MARKETING_MANAGEMENT" => 8,

    ];
}
