@foreach($users as $key => $value)
    @if(in_array($value->id, $arr_ids))
        <tr>
            <td class="text-center"> {{$value -> id ?? ''}} </td>
            <td class="text-center"> {{$value -> firstname ?? ' '}} {{ $value -> lastname ?? '' }} </td>
            <td class="text-center"> {{$data_arr[$value->id]['jobs_applied'] ?? '' }} </td>
            <td class="text-center"> {{$data_arr[$value->id]['jobs_accept'] ?? '' }} </td>
            <td class="text-center"> {{$data_arr[$value->id]['success_rate'] ?? '' }}% </td>
            <td class="text-center"> {{$data_arr[$value->id]['cancel_rate'] ?? '' }}% </td>
            <td class="text-center"> {{$data_arr[$value->id]['jobs_freeze'] ?? '' }} </td>
            <td class="text-center"> {{$data_arr[$value->id]['job_freeze_accept'] ?? '' }} </td>
            <td class="text-center"> {{$data_arr[$value->id]['jobs_frozen_success_rate'] ?? '' }}% </td>
            <td class="text-center"> {{$data_arr[$value->id]['private_job_added'] ?? '' }} </td>
            <td class="text-center">  <a href="{{route('report.singleLocumJobReport',['id' => $value->id])}}"><span class="glyphicon glyphicon-eye-open" style="margin: 0 auto;    display: block; color:#00A9E0;    font-size: 14px;"></span></a> </td>
        </tr>
        <input type="hidden" name="user_id[]" value="{{$value -> id ?? ''}}">
        <input type="hidden" name="locum[]" value="{{$value -> firstname ?? ''}} {{$value -> lastname}}">
        <input type="hidden" name="jobs_applied[]" value="{{$data_arr[$value->id]['jobs_applied'] ?? '' }}">
        <input type="hidden" name="jobs_accepted[]" value="{{$data_arr[$value->id]['jobs_accept'] ?? ''}}">
        <input type="hidden" name="success_rate[]" value="{{$data_arr[$value->id]['success_rate'] ?? ''}}">
        <input type="hidden" name="cancel_rate[]" value="{{$data_arr[$value->id]['cancel_rate'] ?? ''}}">
        <input type="hidden" name="jobs_frozen[]" value="{{$data_arr[$value->id]['jobs_freeze'] ?? ''}}">
        <input type="hidden" name="frozen_and_accepted[]" value="{{$data_arr[$value->id]['job_freeze_accept'] ?? ''}}">
        <input type="hidden" name="frozen_success_rate[]" value="{{$data_arr[$value->id]['jobs_frozen_success_rate'] ?? '' }}">
        <input type="hidden" name="private_jobs_added[]" value="{{$data_arr[$value->id]['private_job_added'] ?? ''}}">

    @endif
@endforeach
