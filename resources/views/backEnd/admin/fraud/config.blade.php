@extends('backEnd.layouts.master')

@section('title', 'Fraud Check Configuration')

@section('content')

@php
    // যদি মডেল App\Models ফোল্ডারে থাকে
    $bd_token = \App\Models\BusinessSetting::where('type', 'bd_courier_api_token')->value('value');
    $bd_status = \App\Models\BusinessSetting::where('type', 'bd_courier_status')->value('value');
    $manual_link = \App\Models\BusinessSetting::where('type', 'manual_check_link')->value('value');
    $manual_status = \App\Models\BusinessSetting::where('type', 'manual_check_status')->value('value');
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Fraud Check Configuration</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">Configuration Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.fraud.update') }}" method="POST">
                        @csrf
                        
                        <div class="form-group row mb-3">
                            <label class="col-md-3 col-form-label">BD Courier API Key</label>
                            <div class="col-md-9">
                                <input type="text" name="bd_courier_api_token" value="{{ $bd_token }}" class="form-control" placeholder="Paste your API Token here">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="col-md-3 col-form-label">Status</label>
                            <div class="col-md-9">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" name="bd_courier_status" id="bd_courier_status" @if($bd_status == 1) checked @endif>
                                    <label class="form-check-label" for="bd_courier_status">Enable/Disable</label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row mb-3">
                            <label class="col-md-3 col-form-label">Manual Check Link</label>
                            <div class="col-md-9">
                                <input type="text" name="manual_check_link" value="{{ $manual_link }}" class="form-control" placeholder="e.g., https://example.com/search?phone=">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="col-md-3 col-form-label">Status</label>
                            <div class="col-md-9">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" name="manual_check_status" id="manual_check_status" @if($manual_status == 1) checked @endif>
                                    <label class="form-check-label" for="manual_check_status">Enable/Disable</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0 text-end">
                            <button type="submit" class="btn btn-primary">Save Configuration</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection