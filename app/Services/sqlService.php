<?php

namespace App\Services;

use App\Models\files_invoice;
use App\Models\notifiaction;
use App\Models\user_token;
use App\Notifications\SendNotification;
use App\Services\JsonResponeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class sqlService extends JsonResponeService
{
    public function sqlForGetInvoicesMaincityAllstate($fk_country, $maincityparam)
    {
        return

            " SELECT
            inv.*,
            us.nameUser,
            cc.name_client,
            cc.name_enterprise,
            cc.fk_regoin,
            rr.name_regoin,
            rrgoin.name_regoin AS name_regoin_invoice,
            cc.type_client,
            cc.mobile,
            cc.ismarketing,
            usr.nameUser AS lastuserupdateName,
            usr1.nameUser AS nameuserinstall,
            usr2.nameUser AS nameuserApprove,
            rr.fk_country,
            usrback.nameUser AS nameuserback,
            userreplay.nameUser AS nameuserreplay,
            usertask.nameUser AS nameusertask,
            cc.city,
            cy.name_city,
            mcit.namemaincity,
            mcit.id_maincity,
            usrinst.nameUser AS nameuser_ready_install,
            usrninst.nameUser AS nameuser_notready_install,
            cc.tag
        FROM
            client_invoice AS inv
            JOIN users AS us ON us.id_user = inv.fk_idUser
            LEFT JOIN users AS usr ON usr.id_user = inv.lastuserupdate
            LEFT JOIN users AS usr1 ON usr1.id_user = inv.userinstall
            LEFT JOIN users AS usrinst ON usrinst.id_user = inv.user_ready_install
            LEFT JOIN users AS usrninst ON usrninst.id_user = inv.user_not_ready_install
            JOIN clients AS cc ON cc.id_clients = inv.fk_idClient
            JOIN city AS cy ON cy.id_city = cc.city
            LEFT JOIN maincity AS mcit ON mcit.id_maincity = cy.fk_maincity
            LEFT JOIN users AS usr2 ON usr2.id_user = inv.iduser_approve
            LEFT JOIN users AS usrback ON usrback.id_user = inv.fkuser_back
            LEFT JOIN users AS userreplay ON userreplay.id_user = inv.fkuserdatareplay
            LEFT JOIN users AS usertask ON usertask.id_user = inv.fkusertask
            JOIN regoin AS rr ON rr.id_regoin = cc.fk_regoin
            JOIN regoin AS rrgoin ON rrgoin.id_regoin = inv.fk_regoin_invoice
            JOIN invoice_product AS i ON i.fk_id_invoice = inv.id_invoice
        WHERE
            rr.fk_country = '$fk_country'
            AND inv.isdelete IS NULL
            AND inv.stateclient = 'مشترك'
            AND inv.isApprove = 1
            AND mcit.id_maincity IN ($maincityparam)
            AND inv.type_seller <> 1
        ORDER BY inv.date_create DESC ";
    }

    public function sqlForGetInvoicesmaincityMix($fk_country, $maincity, $state)
    {
        return

            "SELECT
                inv.*,
                us.nameUser,
                cc.name_client,
                cc.name_enterprise,
                cc.fk_regoin,
                rr.name_regoin,
                rrgoin.name_regoin AS name_regoin_invoice,
                cc.type_client,
                cc.mobile,
                cc.ismarketing,
                usr.nameUser AS lastuserupdateName,
                usr1.nameUser AS nameuserinstall,
                usr2.nameUser AS nameuserApprove,
                rr.fk_country,
                usrback.nameUser AS nameuserback,
                userreplay.nameUser AS nameuserreplay,
                usertask.nameUser AS nameusertask,
                cc.city,
                cy.name_city,
                mcit.namemaincity,
                mcit.id_maincity,
                usrinst.nameUser AS nameuser_ready_install,
                usrninst.nameUser AS nameuser_notready_install,
                cc.tag
            FROM
                client_invoice AS inv
                JOIN users AS us ON us.id_user = inv.fk_idUser
                LEFT JOIN users AS usr ON usr.id_user = inv.lastuserupdate
                LEFT JOIN users AS usr1 ON usr1.id_user = inv.userinstall
                LEFT JOIN users AS usrinst ON usrinst.id_user = inv.user_ready_install
                LEFT JOIN users AS usrninst ON usrninst.id_user = inv.user_not_ready_install
                JOIN clients AS cc ON cc.id_clients = inv.fk_idClient
                JOIN city AS cy ON cy.id_city = cc.city
                LEFT JOIN maincity AS mcit ON mcit.id_maincity = cy.fk_maincity
                LEFT JOIN users AS usr2 ON usr2.id_user = inv.iduser_approve
                LEFT JOIN users AS usrback ON usrback.id_user = inv.fkuser_back
                LEFT JOIN users AS userreplay ON userreplay.id_user = inv.fkuserdatareplay
                LEFT JOIN users AS usertask ON usertask.id_user = inv.fkusertask
                JOIN regoin AS rr ON rr.id_regoin = cc.fk_regoin
                JOIN regoin AS rrgoin ON rrgoin.id_regoin = inv.fk_regoin_invoice
                LEFT JOIN invoice_product AS i ON i.fk_id_invoice = inv.id_invoice
            WHERE
                rr.fk_country = '$fk_country'
                AND inv.isdelete IS NULL
                AND inv.stateclient = 'مشترك'
                AND inv.isApprove = 1
                AND (
                    (
                        '$state' = '0'
                        AND inv.isdoneinstall IS NULL
                        AND inv.ready_install = '0'
                        AND inv.TypeReadyClient = 'suspend'
                    ) OR (
                        '$state' = '1'
                        AND inv.isdoneinstall = 1
                    ) OR (
                        '$state' = 'suspend'
                        AND inv.isdoneinstall IS NULL
                        AND inv.ready_install = '0'
                        AND inv.TypeReadyClient = 'suspend'
                    ) OR (
                        '$state' = 'wait'
                        AND inv.isdoneinstall IS NULL
                        AND inv.ready_install = '1'
                    )
                )
                AND mcit.id_maincity IN ($maincity)
                AND inv.type_seller <> 1
            ORDER BY
                inv.date_create DESC";
    }

    public function sqlForGetInvoicesCityState($fk_country, $city, $param)
    {
        return
            " SELECT
            inv.*,
            us.nameUser,
            cc.name_client,
            cc.name_enterprise,
            cc.fk_regoin,
            rr.name_regoin,
            rrgoin.name_regoin AS name_regoin_invoice,
            cc.type_client,
            cc.mobile,
            cc.ismarketing,
            usr.nameUser AS lastuserupdateName,
            usr1.nameUser AS nameuserinstall,
            usr2.nameUser AS nameuserApprove,
            rr.fk_country,
            usrback.nameUser AS nameuserback,
            userreplay.nameUser AS nameuserreplay,
            usertask.nameUser AS nameusertask,
            cc.city,
            cy.name_city,
            mcit.namemaincity,
            mcit.id_maincity,
            usrinst.nameUser AS nameuser_ready_install,
            usrninst.nameUser AS nameuser_notready_install,
            cc.tag
        FROM
            client_invoice AS inv
            JOIN users AS us ON us.id_user = inv.fk_idUser
            LEFT JOIN users AS usr ON usr.id_user = inv.lastuserupdate
            LEFT JOIN users AS usr1 ON usr1.id_user = inv.userinstall
            LEFT JOIN users AS usrinst ON usrinst.id_user = inv.user_ready_install
            LEFT JOIN users AS usrninst ON usrninst.id_user = inv.user_not_ready_install
            JOIN clients AS cc ON cc.id_clients = inv.fk_idClient
            JOIN city AS cy ON cy.id_city = cc.city
            LEFT JOIN maincity AS mcit ON mcit.id_maincity = cy.fk_maincity
            LEFT JOIN users AS usr2 ON usr2.id_user = inv.iduser_approve
            LEFT JOIN users AS usrback ON usrback.id_user = inv.fkuser_back
            LEFT JOIN users AS userreplay ON userreplay.id_user = inv.fkuserdatareplay
            LEFT JOIN users AS usertask ON usertask.id_user = inv.fkusertask
            JOIN regoin AS rr ON rr.id_regoin = cc.fk_regoin
            JOIN regoin AS rrgoin ON rrgoin.id_regoin = inv.fk_regoin_invoice
        WHERE
            rr.fk_country = $fk_country
            AND inv.isdelete IS NULL
            AND inv.stateclient = 'مشترك'
            AND inv.isApprove = 1
            AND inv.type_seller != 1
            $param

            AND cy.id_city IN ($city)
        ORDER BY
            inv.date_create DESC
    ";
    }

    public function sqlForGetInvoicesCity($fk_country, $city)
    {
        return "SELECT
        inv.*,
        us.nameUser,
        cc.name_client,
        cc.name_enterprise,
        cc.fk_regoin,
        rr.name_regoin,
        rrgoin.name_regoin as name_regoin_invoice,
        cc.type_client,
        cc.mobile,
        cc.ismarketing,
        usr.nameUser as lastuserupdateName,
        usr1.nameUser as nameuserinstall,
        usr2.nameUser as nameuserApprove,
        rr.fk_country,
        usrback.nameUser as nameuserback,
        userreplay.nameUser as nameuserreplay,
        usertask.nameUser as nameusertask,
        cc.city,
        cy.name_city,
        mcit.namemaincity,
        mcit.id_maincity,
        usrinst.nameUser as nameuser_ready_install,
        usrninst.nameUser as nameuser_notready_install,
        cc.tag
    FROM
        client_invoice AS inv
    JOIN
        users AS us ON us.id_user = inv.fk_idUser
    LEFT JOIN
        users AS usr ON usr.id_user = inv.lastuserupdate
    LEFT JOIN
        users AS usr1 ON usr1.id_user = inv.userinstall
    LEFT JOIN
        users AS usrinst ON usrinst.id_user = inv.user_ready_install
    LEFT JOIN
        users AS usrninst ON usrninst.id_user = inv.user_not_ready_install
    JOIN
        clients AS cc ON cc.id_clients = inv.fk_idClient
    JOIN
        city AS cy ON cy.id_city = cc.city
    LEFT JOIN
        maincity AS mcit ON mcit.id_maincity = cy.fk_maincity
    LEFT JOIN
        users AS usr2 ON usr2.id_user = inv.iduser_approve
    LEFT JOIN
        users AS usrback ON usrback.id_user = inv.fkuser_back
    LEFT JOIN
        users AS userreplay ON userreplay.id_user = inv.fkuserdatareplay
    LEFT JOIN
        users AS usertask ON usertask.id_user = inv.fkusertask
    JOIN
        regoin AS rr ON rr.id_regoin = cc.fk_regoin
    JOIN
        regoin AS rrgoin ON rrgoin.id_regoin = inv.fk_regoin_invoice
    WHERE
        rr.fk_country = $fk_country
        AND inv.isdelete IS NULL
        AND inv.stateclient = 'مشترك'
        AND inv.isApprove = 1
        AND inv.type_seller <> 1
        AND cy.id_city IN ($city)
    ORDER BY
        inv.date_create DESC";
    }
}


// -- AND (
//     --         (
//     --             '$state' = '0'
//     --         ) OR (
//     --             '$state' = '1'
//     --             AND inv.isdoneinstall = 1
//     --         ) OR (
//     --             '$state' = 'suspend'
//     --             AND inv.isdoneinstall IS NULL
//     --             AND inv.isdoneinstall = '0'
//     --             AND inv.TypeReadyClient = 'suspend'
//     --         ) OR (
//     --             '$state' = 'wait'
//     --             AND inv.isdoneinstall IS NULL
//     --             AND inv.ready_install = '1'
//     --         )
    // --     )
