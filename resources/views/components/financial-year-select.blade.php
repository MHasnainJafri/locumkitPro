@php
    $creation_month = Auth::user()->created_at->month;
    $register_year = Auth::user()->created_at->year;
    $currentFinanaceYear = date('Y');
    if ($creation_month < $finance_year_start_month) {
        $register_year = $register_year - 1;
    }
    /* Show only last 3 years */
    if ($register_year + 3 <= date('Y')) {
        $register_year = date('Y') - 3;
    }
@endphp
<div class="col-md-12 pad0 select-year-wrapper">
    <div class="financial-year-title col-sm-7 col-md-7 bglightgrey">
        <h4 class="text-right">Financial Year ({{ date('M', mktime(0, 0, 0, $finance_year_start_month, 1)) }}-{{ date('M', mktime(0, 0, 0, $finance_year_start_month + 11, 1)) }} ) : </h4>
    </div>
    <div class="financial-year-select col-sm-5 col-md-5 bglightgrey">
        <div class="form-group">
            <select name="year" class="filter-selection" id="finance-year" onchange="this.form.submit()">
                @for ($i = $register_year; $i <= $currentFinanaceYear; $i++)
                    @php
                        $t = date('n') >= $finance_year_start_month ? $i : $i - 1;
                    @endphp
                    <option value="{{ $i }}" @selected($filter_year == $i)> {{ $t }}-{{ $t + 1 }} </option>
                @endfor
            </select>
        </div>
    </div>
</div>
