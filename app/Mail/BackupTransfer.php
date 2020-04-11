<?php

namespace DBSystem\Mail;

use DBSystem\Backup;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class BackupTransfer extends Mailable
{
    use Queueable, SerializesModels;

    protected $backup;

    /**
     * Create a new message instance.
     *
     * @param Backup $backup
     */
    public function __construct(Backup $backup)
    {
        $this->backup = $backup;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.backup')
            ->with('backup', $this->backup)
            ->attach(Storage::disk('backups')->path($this->backup->name));
    }
}
