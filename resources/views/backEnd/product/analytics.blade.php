@extends('backEnd.layouts.master')
@section('title', 'Top Products')
@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Top View Products</h4>
                </div>
            </div>
        </div>



        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Views</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($topProducts as $item)
                                    <tr>
                                        <td>
                                            {{ $item['product']->name }}
                                        </td>
                                        <td>
                                            {{ $item['views'] }}
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div>

    @endsection
