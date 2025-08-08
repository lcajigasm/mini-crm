<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TemplateMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $subjectText,
        public readonly string $bodyText,
        public readonly ?string $bodyHtml = null,
    ) {}

    public function build(): self
    {
        $this->subject($this->subjectText);
        if ($this->bodyHtml) {
            $this->html($this->bodyHtml);
        } else {
            $this->text('mail.plain')->with(['content' => $this->bodyText]);
        }
        return $this;
    }
}


