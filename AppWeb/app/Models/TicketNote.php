<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketNote extends Model
{
    use HasFactory;

    protected $table = 'ticket_notes';

    protected $fillable = [
        'id_ticket',
        'id_user',
        'note',
        'creation_date',
    ];

    protected $casts = [
        'creation_date' => 'datetime',
    ];

    /**
     * Ticket al que pertenece la nota técnica.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'id_ticket', 'id');
    }

    /**
     * Usuario (técnico o administrador) que redactó la nota.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
