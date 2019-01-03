<?php

namespace Addons\IncomeExpenseReport\Reports;

use IP\Modules\Expenses\Models\Expense;
use IP\Modules\Invoices\Models\Invoice;
use IP\Support\CurrencyFormatter;
use IP\Support\DateFormatter;
use IP\Support\NumberFormatter;
use IP\Support\Statuses\InvoiceStatuses;
use Illuminate\Support\Carbon;

class IncomeExpenseReport
{
    public function getResults($fromDate, $toDate, $companyProfileId = null, $excludeUnpaidInvoices = 0)
    {
        $results = [
            'from_date' => DateFormatter::format($fromDate),
            'to_date' => DateFormatter::format($toDate),
            'total_income' => 0,
            'total_expense' => 0,
            'balance' => 0,
            'records' => [],
        ];

        $invoices = Invoice::with(['client', 'amount'])
            ->where('invoice_date', '>=', $fromDate)
            ->where('invoice_date', '<=', $toDate)
            ->where('invoice_status_id', '<>', InvoiceStatuses::getStatusId('canceled'));

        $expenses = Expense::with(['custom', 'vendor', 'category'])
            ->where('expense_date', '>=', $fromDate)
            ->where('expense_date', '<=', $toDate);

        if ($companyProfileId) {
            $invoices->where('company_profile_id', $companyProfileId);
            $expenses->where('company_profile_id', $companyProfileId);
        }

        if ($excludeUnpaidInvoices) {
            $invoices->paid();
        }

        $invoices = $invoices->get();
        $expenses = $expenses->get();

        $results['records'] = $invoices
        ->merge($expenses)
        ->map(function ($item) {
            $result = [];
            $result['item'] = $item;
            if (isset($item->invoice_date)) {
                // this is an invoice
                $result['type'] = 'invoice';
                $result['carbon'] = $item->invoice_date;
                $result['date'] = $item->formatted_invoice_date;
                $result['clientvendor'] = $item->client->name;
                $result['number'] = $item->number;
                $result['description'] = $item->summary;
                $result['amount'] = $item->amount->formatted_total;

            } else {
                // this is an expense
                
                $result['type'] = 'expense';
                $result['carbon'] = Carbon::parse($item->expense_date);
                $result['date'] = $item->formatted_expense_date;
                $result['clientvendor'] = $item->vendor->name;
                $result['number'] = $item->custom->column_1;
                $result['description'] = $item->category->name;
                $result['amount'] = $item->formatted_amount;
                
            }
            return $result;
        })
        ->sort(function ($a, $b) {
            if ($a['carbon']->lt($b['carbon'])) {
                return -1;
            } elseif ($a['carbon']->gt($b['carbon'])) {
                return 1;
            } else {
                return 0;
            }
        });

        foreach ($results['records'] as $record) {
            if ($record['type'] == 'invoice') {
                $results['balance'] += $record['item']->amount->total;
                $results['total_income'] += $record['item']->amount->total;
            } else {
                $results['balance'] -= $record['item']->amount;
                $results['total_expense'] += $record['item']->amount;
            }
        }

        $results['balance'] = CurrencyFormatter::format($results['balance']);
        $results['total_income'] = CurrencyFormatter::format($results['total_income']);
        $results['total_expense'] = CurrencyFormatter::format($results['total_expense']);

        return $results;
    }

    
}
