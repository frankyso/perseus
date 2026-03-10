<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\StoreTicketReplyRequest;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use Illuminate\Http\RedirectResponse;

class TicketReplyController extends Controller
{
    /**
     * Store a new reply for the specified ticket.
     */
    public function store(StoreTicketReplyRequest $request, Ticket $ticket): RedirectResponse
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);

        $reply = $ticket->replies()->create([
            'body' => $request->validated('body'),
            'user_id' => $request->user()->id,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('ticket-attachments', 'public');

                TicketAttachment::query()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'ticket_id' => $ticket->id,
                    'ticket_reply_id' => $reply->id,
                    'user_id' => $request->user()->id,
                ]);
            }
        }

        return back();
    }
}
