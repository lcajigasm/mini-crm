<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Support\Gdpr\GdprService;
use Illuminate\Console\Command;

class GdprExportCustomer extends Command
{
    protected $signature = 'gdpr:export {customer_id} {--json : Output path only as JSON}';
    protected $description = 'Export customer data (GDPR) to a ZIP file';

    public function handle(GdprService $gdpr): int
    {
        $customer = Customer::findOrFail((int)$this->argument('customer_id'));
        $path = $gdpr->exportCustomerData($customer);
        $this->info('Export generated: '.$path);
        if ($this->option('json')) {
            $this->line(json_encode(['path' => $path]));
        }
        return self::SUCCESS;
    }
}


