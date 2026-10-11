@extends('backEnd.layouts.master') @section('title', 'Product Create') @section('css')
<style>
    .increment_btn,
    .remove_btn {
        margin-top: -17px;
        margin-bottom: 10px;
    }
</style>
<link href="{{ asset('public/backEnd') }}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/backEnd') }}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet"
    type="text/css" />
@endsection @section('content')
<div class="container-fluid">

    <!-- end page title -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('facebook.setting.save') }}" method="POST" class="row"
                        data-parsley-validate="">
                        @csrf
                        <div class="mb-3">

                            <label>Access Token</label>

                            <textarea name="access_token" class="form-control" rows="5">{{ old('access_token', $setting->access_token ?? '') }}</textarea>

                        </div>

                        <div class="mb-3">

                            <label>Pixel ID</label>

                            <input type="text" name="pixel_id" class="form-control"
                                value="{{ old('pixel_id', $setting->pixel_id ?? '') }}">

                        </div>

                        <div class="mb-3">

                            <label>Test Event Code</label>

                            <input type="text" name="test_event_code" class="form-control"
                                value="{{ old('test_event_code', $setting->test_event_code ?? '') }}">

                        </div>

                        <div class="form-check">

                            <input type="checkbox" name="browser_tracking" value="1" class="form-check-input"
                                {{ $setting->browser_tracking ?? true ? 'checked' : '' }}>

                            <label>Enable Browser Tracking</label>

                        </div>

                        <div class="form-check">

                            <input type="checkbox" name="server_tracking" value="1" class="form-check-input"
                                {{ $setting->server_tracking ?? true ? 'checked' : '' }}>

                            <label>Enable Server Tracking</label>

                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-5">
                                <button class="btn btn-primary">

                                    Save Configuration

                                </button>
                            </div>
                            <div class="col-md-5">
                                <button type="button" id="verifyBtn" class="btn btn-success">

                                    Verify Connection

                                </button>
                            </div>
                        </div>
                        <hr>

                        @if (isset($setting))

                            @if ($setting->is_connected)
                                <div class="alert alert-success">

                                    <b>Status :</b>

                                    Connected

                                </div>
                            @else
                                <div class="alert alert-danger">

                                    <b>Status :</b>

                                    Disconnected

                                </div>
                            @endif

                        @endif



                    </form>
                </div>
                <!-- end card-body-->
            </div>
            <!-- end card-->
        </div>
        <!-- end col-->
    </div>
</div>
@endsection

@section('script')
<script>
    $('#verifyBtn').click(function() {

        $.ajax({

            url: "{{ route('facebook.setting.verify') }}",

            method: 'POST',

            data: {

                _token: "{{ csrf_token() }}"

            },

            success: function(res) {

                alert(res.message);

                location.reload();

            }

        });

    });
</script>

@endsection
