<?php

namespace App\Mail;

use App\Models\Requisicion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CotizacionesRechazadas extends Mailable
{
    use Queueable, SerializesModels;

     public $requisicion;
    /**
     * Create a new message instance.
     */
     public function __construct(Requisicion $requisicion, Collection $datos)
    {
        $this->requisicion = $requisicion;
         $this->datos = $datos;
    }

      public function build()
    {
        return $this->view('emails.cotizacion-rechazada')
                    ->with(['requisicion' => $this->requisicion, 'datos'=> $this->datos]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cotizacion Rechazada',
        );
    }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'requisicion-creada',
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
