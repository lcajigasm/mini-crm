<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Support\Gdpr\GdprService;
use Illuminate\Console\Command;

class GdprEraseCustomer extends Command
{
    protected $signature = 'gdpr:erase {customer_id} {--force : Do not prompt for confirmation}';
    protected $description = 'Anonymize customer personal data (GDPR right to be forgotten)';

    public function handle(GdprService $gdpr): int
    {
        $customer = Customer::findOrFail((int)$this->argument('customer_id'));
        if (!$this->option('force')) {
            if (! $this->confirm('This will anonymize personal data for customer #'.$customer->id.' ("'.$customer->name.'"). Continue?')) {
                $this->warn('Aborted.');
                return self::INVALID;
            }
        }
        $gdpr->eraseCustomer($customer);
        $this->info('Customer anonymized.');
        return self::SUCCESS;
    }
}



