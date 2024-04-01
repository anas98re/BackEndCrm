<?php
namespace App\Scripts;

use App\Constants;
use App\Models\category_ticket_fk;
use App\Models\subcategory_ticket_fk;
use App\Models\ticket_detail;
use App\Models\tickets;
use Carbon\Carbon;

class TransferTicketTable
{
    public static function transfer()
    {
        $tickets = tickets::query()->whereNotNull('date_open')->get();

        foreach($tickets as $ticket)
        {

            $tag = \Str::random(5);

            ticket_detail::create([
                'fk_ticket' => $ticket->id_ticket,
                'fk_state' => Constants::TICKET_OPEN,
                'tag' => $tag,
                'notes' => is_null($ticket->date_recive)? $ticket->notes_ticket : ' ',
                'fk_user' => $ticket->fk_user_open,
                'date_state' => $ticket->date_open == '0000-00-00 00:00:00'? Carbon::now() : $ticket->date_open,
            ]);
            if(! is_null($ticket->date_recive) )
            {
                ticket_detail::create([
                    'fk_ticket' => $ticket->id_ticket,
                    'fk_state' => Constants::TICKET_RECIVE,
                    'tag' => $tag,
                    'notes' => is_null($ticket->date_close)? $ticket->notes_ticket : ' ',
                    'fk_user' => $ticket->fk_user_recive,
                    'date_state' => $ticket->date_recive,
                ]);
            }
            if(! is_null($ticket->date_close) )
            {
                ticket_detail::create([
                    'fk_ticket' => $ticket->id_ticket,
                    'fk_state' => Constants::TICKET_CLOSE,
                    'tag' => $tag,
                    'notes' => is_null($ticket->suspend_date)? $ticket->notes_ticket : ' ',
                    'fk_user' => $ticket->fk_user_close,
                    'date_state' => $ticket->date_close,
                ]);

                if(! is_null($ticket->categories_ticket_fk) )
                {
                    category_ticket_fk::create([
                        'fk_category' => $ticket->categories_ticket_fk,
                        'fk_ticket' => $ticket->id_ticket,
                    ]);
                }
                if(! is_null($ticket->subcategories_ticket_fk) )
                {
                    subcategory_ticket_fk::create([
                        'fk_subcategory' => $ticket->subcategories_ticket_fk,
                        'fk_ticket' => $ticket->id_ticket,
                    ]);
                }
            }
            if(! is_null($ticket->suspend_date) )
            {
                ticket_detail::create([
                    'fk_ticket' => $ticket->id_ticket,
                    'fk_state' => Constants::TICKET_SUSPEND,
                    'tag' => $tag,
                    'notes' => $ticket->notes_ticket?? " ",
                    'fk_user' => $ticket->suspend_id,
                    'date_state' => $ticket->suspend_date,
                ]);
            }

            if(! is_null($ticket->date_rate) )
            {
                ticket_detail::create([
                    'fk_ticket' => $ticket->id_ticket,
                    'fk_state' => Constants::TICKET_RATE,
                    'tag' => $tag,
                    'notes' => $ticket->notes_rate?? " ",
                    'fk_user' => $ticket->fkuser_rate,
                    'date_state' => $ticket->date_rate,
                ]);
            }
        }
    }
}
