<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Support\Gdpr\GdprService;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\Consent;
use App\Models\CallLog;
use App\Models\WhatsAppMessage;
use App\Models\EmailMessage;

class ContactsController extends Controller
{
    public function index()
    {
        $customers = Customer::orderByDesc('created_at')->limit(10)->get();
        return view('crm.contacts.index', compact('customers'));
    }

    public function export(Customer $customer, GdprService $gdpr)
    {
        try {
            $zipPath = $gdpr->exportCustomerData($customer);
            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            $export = [
                'customer' => $customer->toArray(),
                'consents' => Consent::where('customer_id', $customer->id)->get()->toArray(),
                'appointments' => $customer->appointments()->get()->toArray(),
                'call_logs' => CallLog::where('customer_id', $customer->id)->get()->toArray(),
                'whatsapp_messages' => WhatsAppMessage::where('customer_id', $customer->id)->get()->toArray(),
                'email_messages' => EmailMessage::where('customer_id', $customer->id)->get()->toArray(),
                'generated_at' => now()->toIso8601String(),
            ];
            AuditLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => 'gdpr.export',
                'target_type' => Customer::class,
                'target_id' => $customer->id,
                'metadata' => ['fallback' => 'json'],
                'ip_address' => request()->ip(),
            ]);
            $filename = 'customer-'.$customer->id.'.json';
            return response()->streamDownload(function () use ($export) {
                echo json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }, $filename, [
                'Content-Type' => 'application/json',
            ]);
        }
    }

    public function erase(Customer $customer, GdprService $gdpr)
    {
        $gdpr->eraseCustomer($customer);
        return back()->with('status', 'Cliente anonimizado correctamente');
    }
}
