<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Jobs\SendTemplateMessageJob;
use App\Models\Customer;
use App\Models\Template;
use App\Models\WhatsAppMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessagingController extends Controller
{
    public function index()
    {
        $templates = Template::orderBy('name')->get();
        $customers = Customer::orderByDesc('created_at')->limit(10)->get();
        $messages = WhatsAppMessage::orderByDesc('created_at')->limit(10)->get();
        return view('crm.messaging.index', compact('templates', 'customers', 'messages'));
    }

    public function preview(Template $template)
    {
        return response()->json([
            'id' => $template->id,
            'key' => $template->key,
            'name' => $template->name,
            'channel' => $template->channel,
            'subject' => $template->subject,
            'content_text' => $template->content_text,
            'content_html' => $template->content_html,
        ]);
    }

    public function send(Request $request, Template $template): RedirectResponse
    {
        $customerId = (int) $request->input('customer_id');
        $phone = $request->input('phone');
        $email = $request->input('email');
        SendTemplateMessageJob::dispatch($template->key, $customerId ?: null, null, [], $phone ?: null, $email ?: null);
        return redirect()->route('messaging.index')->with('status', 'Plantilla encolada para envío');
    }
}



