<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReply extends Model
{
    protected $fillable = [
        'ticket_id',
        'replier_type',
        'replier_id',
        'replier_name',
        'replier_email',
        'message',
        'is_internal',
        'attachments'
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'attachments' => 'array'
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function replier()
    {
        return $this->morphTo();
    }
}

