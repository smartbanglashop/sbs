@extends('backEnd.layouts.master')
@section('title',$order_status->name.' Order')
@section('content')
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('admin.order.create')}}" class="btn btn-danger rounded-pill"><i class="fe-shopping-cart"></i> Add New</a>
                </div>
                <h4 class="page-title">{{$order_status->name}} Order ({{$order_status->orders_count}})</h4>
            </div>
        </div>
    </div>        
    <div class="row order_page">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-8">
                        <ul class="action2-btn">
                            <li><a data-bs-toggle="modal" data-bs-target="#asignUser" class="btn rounded-pill btn-success"><i class="fe-plus"></i> Assign User</a></li>
                            <li><a data-bs-toggle="modal" data-bs-target="#changeStatus" class="btn rounded-pill btn-primary"><i class="fe-plus"></i> Change Status</a></li>
                            <li><a href="{{route('admin.order.bulk_destroy')}}" class="btn rounded-pill btn-danger order_delete"><i class="fe-plus"></i> Delete All</a></li>
                            <li><a href="{{route('admin.order.order_print')}}" class="btn rounded-pill btn-info multi_order_print"><i class="fe-printer"></i> Print</a></li>
                            @if($steadfast)
                            <li><a href="{{route('admin.bulk_courier', 'steadfast')}}?status=5" class="btn rounded-pill btn-info multi_order_courier"><i class="fe-truck"></i> Steadfast</a></li>
                            @endif
                            <li><a data-bs-toggle="modal" data-bs-target="#pathao" class="btn rounded-pill btn-info"><i class="fe-truck"></i> pathao</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-4">
                        <form class="custom_form">
                            <div class="form-group">
                                <input type="text" name="keyword" placeholder="Search">
                                <button class="btn  rounded-pill btn-info">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive ">
                <table id="datatable-buttons" class="table table-striped   w-100">
                    <thead>
                        <tr>
                            <th style="width:2%"><div class="form-check"><label class="form-check-label"><input type="checkbox" class="form-check-input checkall" value=""></label>
                            <th style="width:2%">SL</th>
                                    </div></th>
                            <th style="width:8%">Action</th>
                            <th style="width:8%">Invoice</th>
                            <th style="width:10%">Date</th>
                            <th style="width:10%">Name</th>
                            <th style="width:10%">Phone</th>
                            <th style="width:10%">Assign</th>
                            <th style="width:10%">Amount</th>
                            <th style="width:10%">Order Note</th>
                            <th style="width:10%">Status</th>
                               <th style="width:10%">Coureir Status</th>
                            <th style="width:10%">Coureir Note</th>
                        </tr>
                    </thead>
                
                
                    <tbody>
                        @foreach($show_data as $key=>$value)
                        <tr>
                            <td><input type="checkbox" class="checkbox" value="{{$value->id}}"></td>
                            <td>{{$loop->iteration}}</td>
                            <td>
                                <div class="button-list custom-btn-list">   
                                    <a href="javascript:void(0)" class="btn-check-fraud" data-phone="{{$value->shipping?$value->shipping->phone:''}}" title="Check Fraud" style="color: #6c757d; margin-right: 5px;">
                                        <i class="fe-shield"></i>
                                    </a>
                                    <a href="{{route('admin.order.invoice',['invoice_id'=>$value->invoice_id])}}" title="Invoice"><i class="fe-eye"></i></a>
                                      @if($value->courierShipment)
                                      {{-- <a href="{{route('admin.order.coureir',['invoice_id'=>$value->invoice_id])}}" title="Coureir"><i class="fe-truck"></i></i></a> --}}
                                      <a href="{{ route('admin.order.courier_print',$value->courierShipment->id) }}"
   title="Coureir">

    <i class="fe-truck"></i></i>

</a>
                                    @endif
                                    <a href="{{route('admin.order.process',['invoice_id'=>$value->invoice_id])}}" title="Process"><i class="fe-settings"></i></a>
                                    <a href="{{route('admin.order.edit',['invoice_id'=>$value->invoice_id])}}" title="Edit"><i class="fe-edit"></i></a>
                                    <form method="post" action="{{route('admin.order.destroy')}}" class="d-inline">        
                                        @csrf
                                    <input type="hidden" value="{{$value->id}}" name="id">
                                    <button type="submit" title="Delete" class="delete-confirm"><i class="fe-trash-2"></i></button></form>
                                </div>
                            </td>
                            <td>{{$value->invoice_id}}</td>
                            <td>{{date('d-m-Y', strtotime($value->updated_at))}}<br> {{date('h:i:s a', strtotime($value->updated_at))}}</td>
                            <td><strong>{{$value->shipping?$value->shipping->name:''}}</strong><p>{{$value->shipping?$value->shipping->address:''}}</p></td>
                            <td>{{$value->shipping?$value->shipping->phone:''}}</td>
                            <td>{{$value->user?$value->user->name:''}}</td>
                            <td>৳{{$value->amount}}</td>
                             <td>{{$value->order_note}}</td>
                            <td>{{$value->status?$value->status->name:''}}</td>
                                <td>{{$value->steadfast_status?$value->steadfast_status:''}}</td>
                            <td>{{$value->note?$value->note :''}}</td>   
                            
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                <div class="custom-paginate">
                    {{$show_data->links('pagination::bootstrap-4')}}
                </div>
            </div> </div> </div></div>
</div>

<div class="modal fade" id="asignUser" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Assign User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{route('admin.order.assign')}}" id="order_assign">
      <div class="modal-body">
        <div class="form-group">
            <select name="user_id" id="user_id" class="form-control">
                <option value="">Select..</option>
                @foreach($users as $key=>$value)
                <option value="{{$value->id}}">{{$value->name}}</option>
                @endforeach
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="changeStatus" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Assign User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{route('admin.order.status')}}" id="order_status_form">
      <div class="modal-body">
        <div class="form-group">
            <select name="order_status" id="order_status" class="form-control">
                <option value="">Select..</option>
                @foreach($orderstatus as $key=>$value)
                <option value="{{$value->id}}">{{$value->name}}</option>
                @endforeach
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="pathao" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pathao Courier</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{route('admin.order.pathao')}}" id="order_sendto_pathao">

      <div class="modal-body">
        <div class="form-group">
            <label for="pathaostore" class="form-label">Store</label>
           <select name="pathaostore" id="pathaostore" class="pathaostore form-control" >
             <option value="">Select Store...</option>
             @if(isset($pathaostore['data']['data']))
             @foreach($pathaostore['data']['data'] as $key=>$store)
             <option value="{{$store['store_id']}}">{{$store['store_name']}}</option>
             @endforeach
             @else
             @endif
           </select>
            @if ($errors->has('pathaostore'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('pathaostore') }}</strong>
              </span>
              @endif
        </div>
        <div class="form-group mt-3">
          <label for="pathaocity" class="form-label">City</label>
           <select name="pathaocity" id="pathaocity" class="chosen-select pathaocity form-control" style="width:100%" >
             <option value="">Select City...</option>
             @if(isset($pathaocities['data']['data']))
             @foreach($pathaocities['data']['data'] as $key=>$city)
             <option value="{{$city['city_id']}}">{{$city['city_name']}}</option>
             @endforeach
             @else
             @endif
           </select>
            @if ($errors->has('pathaocity'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('pathaocity') }}</strong>
              </span>
              @endif
        </div>
        <div class="form-group mt-3">
          <label for="" class="form-label">Zone</label>
             <select name="pathaozone" id="pathaozone" class="pathaozone chosen-select form-control  {{ $errors->has('pathaozone') ? ' is-invalid' : '' }}" value="{{ old('pathaozone') }}"  style="width:100%">
            </select>
             @if ($errors->has('pathaozone'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('pathaozone') }}</strong>
              </span>
              @endif
        </div>
        <div class="form-group mt-3">
          <label for="" class="form-label">Area</label>
             <select name="pathaoarea" id="pathaoarea" class="pathaoarea chosen-select form-control  {{ $errors->has('pathaoarea') ? ' is-invalid' : '' }}" value="{{ old('pathaoarea') }}"  style="width:100%">
            </select>
             @if ($errors->has('pathaoarea'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('pathaoarea') }}</strong>
              </span>
              @endif
        </div>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="fraudCheckModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Fraud Check Analysis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="fraud_check_body">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $(".checkall").on('change',function(){
      $(".checkbox").prop('checked',$(this).is(":checked"));
    });

    // Fraud Check Script (Final Undefined Fixed)
    $(document).on('click', '.btn-check-fraud', function() {
        var phone = $(this).data('phone');
        
        if(!phone) {
            toastr.error('Phone number not found!');
            return;
        }

        $('#fraudCheckModal').modal('show');
        $('#fraud_check_body').html('<div class="text-center"><div class="spinner-border text-primary"></div><p>Checking...</p></div>');

        $.post('{{ route('admin.fraud.check') }}', {
            _token: '{{ csrf_token() }}',
            phone: phone
        }, function(data) {
            
            // 1. Shop History
            var internalHtml = `
                <div class="card bg-light mb-3" style="border: 1px solid #ddd;">
                    <div class="card-header font-weight-bold" style="background:#f8f9fa;">Shop History</div>
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between text-center">
                            <div><small>Total</small><h6 class="mb-0 font-weight-bold">${data.internal.total}</h6></div>
                            <div><small>Success</small><h6 class="mb-0 text-success font-weight-bold">${data.internal.success}</h6></div>
                            <div><small>Cancel</small><h6 class="mb-0 text-danger font-weight-bold">${data.internal.cancel}</h6></div>
                        </div>
                    </div>
                </div>`;

            // 2. BD Courier API Check
            var externalHtml = '';
            
            if(data.external && data.external.status === 'success') { 
                externalHtml = `
                <div class="card border-info mb-3" style="border: 1px solid #17a2b8;">
                    <div class="card-header bg-soft-info font-weight-bold text-info" style="color: #17a2b8;">BD Courier Global Check</div>
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between text-center">
                            <div><small>Total</small><h6 class="mb-0">${data.external.total}</h6></div>
                            <div><small>Success</small><h6 class="mb-0 text-success">${data.external.success}</h6></div>
                            <div><small>Cancel</small><h6 class="mb-0 text-danger">${data.external.cancel}</h6></div>
                        </div>
                        <div class="text-center mt-2 border-top pt-1">
                            <small>Success Rate: <strong>${data.external.rate}%</strong></small>
                        </div>
                    </div>
                </div>`;
            } else {
                 var msg = data.api_msg ? data.api_msg : 'Data Not Available';
                 externalHtml = `<div class="alert alert-secondary text-center small">${msg}</div>`;
            }

            // 3. Manual Check Link (FIXED: Undefined solved)
            var manualHtml = '';
            if(data.manual_status == 1) {
                // লিঙ্কটি ভেরিয়েবলে নেওয়া হচ্ছে এবং ট্রিম করা হচ্ছে
                var finalLink = data.manual_link ? data.manual_link.trim() : '';
                
                manualHtml = `
                <div class="input-group mt-3">
                    <input type="text" class="form-control form-control-sm" value="${data.phone}" id="fraudPhoneInput" readonly>
                    <div class="input-group-append">
                        <button class="btn btn-success btn-sm" type="button" onclick="copyAndRedirect('${finalLink}')">
                            Check
                        </button>
                    </div>
                </div>
                <small class="text-muted d-block text-center mt-1">* Number copied! Please paste in the opened tab.</small>
                `;
            }

            $('#fraud_check_body').html(internalHtml + externalHtml + manualHtml);
        });
    });

    // Order Assign
    $(document).on('submit', 'form#order_assign', function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        let user_id=$(document).find('select#user_id').val();
        var order = $('input.checkbox:checked').map(function(){ return $(this).val(); });
        var order_ids=order.get();
        if(order_ids.length ==0){ toastr.error('Please Select An Order First !'); return; }
        $.ajax({ type:'GET', url:url, data:{user_id,order_ids}, success:function(res){
               if(res.status=='success'){ toastr.success(res.message); window.location.reload(); }else{ toastr.error('Failed'); }
        }});
    });

    // Order Status
    $(document).on('submit', 'form#order_status_form', function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        let order_status=$(document).find('select#order_status').val();
        var order = $('input.checkbox:checked').map(function(){ return $(this).val(); });
        var order_ids=order.get();
        if(order_ids.length ==0){ toastr.error('Please Select An Order First !'); return; }
        $.ajax({ type:'GET', url:url, data:{order_status,order_ids}, success:function(res){
               if(res.status=='success'){ toastr.success(res.message); window.location.reload(); }else{ toastr.error('Failed'); }
        }});
    });

    // Order Delete
    $(document).on('click', '.order_delete', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var order = $('input.checkbox:checked').map(function(){ return $(this).val(); });
        var order_ids=order.get();
        if(order_ids.length ==0){ toastr.error('Please Select An Order First !'); return; }
        $.ajax({ type:'GET', url:url, data:{order_ids}, success:function(res){
               if(res.status=='success'){ toastr.success(res.message); window.location.reload(); }else{ toastr.error('Failed'); }
        }});
    });
    
    // Multi Print
    $(document).on('click', '.multi_order_print', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var order = $('input.checkbox:checked').map(function(){ return $(this).val(); });
        var order_ids=order.get();
        if(order_ids.length ==0){ toastr.error('Please Select Atleast One Order!'); return; }
        $.ajax({ type:'GET', url, data:{order_ids}, success:function(res){
               if(res.status=='success'){ var myWindow = window.open("", "_blank"); myWindow.document.write(res.view); }else{ toastr.error('Failed'); }
        }});
    });

    // Multi Courier
    $(document).on('click', '.multi_order_courier', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var order = $('input.checkbox:checked').map(function(){ return $(this).val(); });
        var order_ids=order.get();
        if(order_ids.length ==0){ toastr.error('Please Select An Order First !'); return; }
        $.ajax({ type:'GET', url:url, data:{order_ids}, success:function(res){
               if(res.status=='success'){ toastr.success(res.message); window.location.reload(); }else{ toastr.error('Failed'); }
        }});
    });
});

// Manual Link Function (Undefined Fixed)
function copyAndRedirect(link) {
    // ১. নাম্বার কপি করা
    var copyText = document.getElementById("fraudPhoneInput");
    if(copyText) {
        copyText.select();
        document.execCommand("copy");
    }
    
    // ২. লিংক ওপেন করা (কোনো কিছু যোগ না করে)
    if(link && link.length > 5) {
        window.open(link, '_blank');
    } else {
        toastr.warning('Invalid Link Saved!');
    }
}
</script>
@endsection