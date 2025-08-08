<?php

namespace App\Jobs;

use App\Domain\Ports\WhatsAppPort;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\EmailMessage;
use App\Models\Template;
use App\Models\WhatsAppMessage;
use App\Support\TemplateRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\TemplateMailable;

class SendTemplateMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly string $templateKey,
        public readonly ?int $customerId = null,
        public readonly ?int $leadId = null,
        public readonly array $variables = [],
        public readonly ?string $overridePhone = null,
        public readonly ?string $overrideEmail = null,
    ) {}

    public function handle(WhatsAppPort $whatsApp): void
    {
        $template = Template::where('key', $this->templateKey)->where('active', true)->first();
        if (!$template) {
            Log::warning('Template not found or inactive', ['key' => $this->templateKey]);
            return;
        }

        $customer = $this->customerId ? Customer::find($this->customerId) : null;
        $toPhone = $this->overridePhone ?: ($customer?->phone ?? '');
        $toEmail = $this->overrideEmail ?: ($customer?->email ?? '');

        $context = $this->variables;
        if ($customer) {
            $context['customer'] = [
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
            ];
        }

        // WhatsApp
        if (in_array($template->channel, ['whatsapp','both'], true) && $toPhone) {
            $renderedText = TemplateRenderer::render($template->content_text, $context);
            $message = WhatsAppMessage::create([
                'customer_id' => $customer?->id,
                'lead_id' => $this->leadId,
                'user_id' => null,
                'phone' => $toPhone,
                'direction' => 'outbound',
                'message' => $renderedText,
                'status' => 'queued',
            ]);

            try {
                $whatsApp->sendTemplate($toPhone, $template->whatsapp_template ?: $template->key, $context);
                $message->status = 'sent';
                $message->sent_at = now();
                $message->save();
            } catch (\Throwable $e) {
                $message->status = 'failed';
                $message->save();
                Log::error('Failed sending WhatsApp', ['error' => $e->getMessage()]);
            }
        }

        // Email via simple mailable
        if (in_array($template->channel, ['email','both'], true) && $toEmail) {
            $subject = TemplateRenderer::render($template->subject ?? $template->name, $context);
            $bodyText = TemplateRenderer::render($template->content_text, $context);

            $emailLog = EmailMessage::create([
                'customer_id' => $customer?->id,
                'lead_id' => $this->leadId,
                'user_id' => null,
                'from_email' => config('mail.from.address'),
                'to_email' => $toEmail,
                'subject' => $subject,
                'body' => $template->content_html ? TemplateRenderer::render($template->content_html, $context) : $bodyText,
            ]);

            try {
                $mailable = new TemplateMailable(
                    subjectText: $subject,
                    bodyText: $bodyText,
                    bodyHtml: $template->content_html ? TemplateRenderer::render($template->content_html, $context) : null,
                );
                Mail::to($toEmail)->send($mailable);
                $emailLog->sent_at = now();
                $emailLog->save();
            } catch (\Throwable $e) {
                Log::error('Failed sending Email', ['error' => $e->getMessage()]);
            }
        }
    }
}


