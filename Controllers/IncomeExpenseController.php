<?php

namespace Addons\IncomeExpenseReport\Controllers;

use IP\Http\Controllers\Controller;
use Addons\IncomeExpenseReport\Reports\IncomeExpenseReport;
use IP\Modules\Reports\Requests\DateRangeRequest;
use IP\Support\PDF\PDFFactory;
use IP\Modules\CompanyProfiles\Models\CompanyProfile;


class IncomeExpenseController extends Controller {

    private $report;

    public function __construct(IncomeExpenseReport $report)
    {
        $this->report = $report;
    }

    public function index() {
        return view('incomeexpense.options', ['companyProfiles' => ['' => trans('fi.all_company_profiles')] + CompanyProfile::getList()]);
    }

    public function validateOptions(DateRangeRequest $request)
    {

    }

    public function html()
    {
        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('exclude_unpaid_invoices')
        );

        return view('incomeexpense.output')
            ->with('results', $results);
    }

    public function pdf()
    {
        $pdf = PDFFactory::create();

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('exclude_unpaid_invoices')
        );

        $html = view('incomeexpense.output')
            ->with('results', $results)->render();

        $pdf->download($html, trans('IncomeExpenseReport::common.filename') . '.pdf');
    }
}
