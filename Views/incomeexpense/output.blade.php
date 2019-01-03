@extends('reports.layouts.master')

@section('content')

    <style>
        .totals_label {
            font-weight:bold;
        }
    </style>

    <h1 style="margin-bottom: 0;">{{ trans('IncomeExpenseReport::common.title') }}</h1>
    <h3 style="margin-top: 0;">{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>

    <table class="alternate">

        <thead>
        <tr>
            <th style="width: 20%;">{{ trans('fi.date') }}</th>
            <th style="width: 20%;">{{ trans('IncomeExpenseReport::report.clientvendor') }}</th>
            <th style="width: 20%;">{{ trans('IncomeExpenseReport::report.reference') }}</th>
            <th style="width: 20%;">{{ trans('fi.description') }}</th>
            <th class="amount" style="width: 20%;">{{ trans('fi.amount') }}</th>
        </tr>
        </thead>

        <tbody>
        @foreach ($results['records'] as $result)
            <tr>
                <td>{{ $result['date'] }}</td>
                <td>{{ $result['clientvendor'] }}</td>
                <td>{{ $result['number'] }}</td>
                <td>{{ $result['description'] }}</td>
                <td class="amount">{{ $result['amount'] }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="3"></td>
            <td class="totals_label">
                {{ trans('fi.income') }}
            </td>
            <td class="totals_value amount">
                {{ $results['total_income']}}
            </td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td class="totals_label">
                {{ trans('fi.expenses') }}
            </td>
            <td class="totals_value amount">
                {{ $results['total_expense']}}
            </td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td class="totals_label">
                {{ trans('fi.balance') }}
            </td>
            <td class="totals_value amount">
                {{ $results['balance']}}
            </td>
        </tr>
        </tbody>

    </table>

@stop