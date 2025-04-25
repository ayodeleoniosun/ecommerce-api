<?php

namespace App\Application\Listeners\Auth;

use App\Application\Events\Auth\VerificationMailResentEvent;
use App\Application\Mail\Auth\UserVerificationResentMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ResendVerification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(VerificationMailResentEvent $event): void
    {
        Mail::to($event->verification->user->email)->send(new UserVerificationResentMail($event->verification));
    }
}
