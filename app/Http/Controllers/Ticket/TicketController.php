<?php

namespace App\Http\Controllers\Ticket;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    /**
     * Display a listing of the user's tickets.
     */
    public function index(Request $request): Response
    {
        $tickets = Ticket::query()
            ->where('user_id', $request->user()->id)
            ->with(['department', 'category', 'assignedAgent'])
            ->when($request->input('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->input('priority'), fn ($query, $priority) => $query->where('priority', $priority))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('tickets/index', [
            'tickets' => $tickets,
            'filters' => $request->only(['status', 'priority']),
        ]);
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create(): Response
    {
        return Inertia::render('tickets/create', [
            'departments' => Department::query()->where('is_active', true)->get(),
            'categories' => TicketCategory::query()->where('is_active', true)->get(),
        ]);
    }

    /**
     * Store a newly created ticket.
     */
    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $ticket = Ticket::query()->create([
            ...$request->safe()->except('attachments'),
            'user_id' => $request->user()->id,
            'status' => TicketStatus::Open,
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
                    'user_id' => $request->user()->id,
                ]);
            }
        }

        return to_route('tickets.show', $ticket);
    }

    /**
     * Display the specified ticket.
     */
    public function show(Request $request, Ticket $ticket): Response
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);

        $ticket->load([
            'department',
            'category',
            'assignedAgent',
            'attachments',
            'replies' => fn ($query) => $query->where('is_internal_note', false)->with(['user', 'attachments'])->oldest(),
        ]);

        return Inertia::render('tickets/show', [
            'ticket' => $ticket,
        ]);
    }
}
