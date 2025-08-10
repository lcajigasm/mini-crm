<?php

namespace App\Support\Gdpr;

use App\Models\AuditLog;
use App\Models\CallLog;
use App\Models\Consent;
use App\Models\Customer;
use App\Models\EmailMessage;
use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class GdprService
{
    public function recordConsent(Customer $customer, string $channel, bool $granted, ?string $source = null): Consent
    {
        $consent = Consent::updateOrCreate(
            ['customer_id' => $customer->id, 'channel' => $channel],
            [
                'granted' => $granted,
                'granted_at' => $granted ? now() : null,
                'revoked_at' => $granted ? null : now(),
                'source' => $source,
            ]
        );

        $this->audit('consent.'.($granted ? 'grant' : 'revoke'), $customer, [
            'channel' => $channel,
            'source' => $source,
        ]);

        return $consent;
    }

    public function exportCustomerData(Customer $customer): string
    {
        $export = [
            'customer' => $customer->toArray(),
            'consents' => Consent::where('customer_id', $customer->id)->get()->toArray(),
            'appointments' => $customer->appointments()->get()->toArray(),
            'call_logs' => CallLog::where('customer_id', $customer->id)->get()->toArray(),
            'whatsapp_messages' => WhatsAppMessage::where('customer_id', $customer->id)->get()->toArray(),
            'email_messages' => EmailMessage::where('customer_id', $customer->id)->get()->toArray(),
            'generated_at' => now()->toIso8601String(),
        ];

        $disk = Storage::disk('local');
        $baseDir = 'exports/gdpr';
        $disk->makeDirectory($baseDir);
        $baseName = $customer->id.'-'.now()->format('YmdHis');
        $zipPath = $baseDir.'/'.$baseName.'.zip';
        $fullZip = storage_path('app/'.$zipPath);

        $zip = new ZipArchive();
        if ($zip->open($fullZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Unable to create ZIP');
        }
        $zip->addFromString('customer.json', json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $zip->close();

        $this->audit('gdpr.export', $customer, ['path' => $zipPath]);

        return $fullZip;
    }

    public function eraseCustomer(Customer $customer): void
    {
        DB::transaction(function () use ($customer) {
            // Break personal link without destroying historical records
            $customer->update([
                'name' => 'Anónimo #'.$customer->id,
                'email' => null,
                'phone' => null,
                'secondary_phone' => null,
                'notes' => null,
            ]);

            // Null out FK references in child records that may expose PII
            CallLog::where('customer_id', $customer->id)->update(['customer_id' => null, 'phone' => null, 'notes' => null]);
            WhatsAppMessage::where('customer_id', $customer->id)->update(['customer_id' => null, 'phone' => null, 'message' => null, 'status' => null]);
            EmailMessage::where('customer_id', $customer->id)->update(['customer_id' => null, 'from_email' => null, 'to_email' => null, 'subject' => null, 'body' => null, 'message_id' => null]);

            // Appointments keep schedule but drop notes and unlink
            $customer->appointments()->update(['customer_id' => null, 'notes' => null, 'location' => null]);
            // Treatments unlink and clear notes
            if (method_exists($customer, 'treatments')) {
                $customer->treatments()->update(['customer_id' => null, 'notes' => null]);
            }

            // Consents soft-delete for audit but clear PII timestamps/source retained
            Consent::where('customer_id', $customer->id)->update(['granted' => false, 'revoked_at' => now()]);
        });

        $this->audit('gdpr.erase', $customer);
    }

    private function audit(string $action, Customer $customer, array $metadata = []): void
    {
        $ip = app()->bound('request') ? request()->ip() : null;
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'target_type' => Customer::class,
            'target_id' => $customer->id,
            'metadata' => $metadata,
            'ip_address' => $ip,
        ]);
    }
}


