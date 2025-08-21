@extends('admin.layouts.master')
@section('title', 'Edit KYC Method')
@section('page_content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Edit KYC Method</h3> 
                </div>
                <form method="POST" action="{{ url('admin/settings/edit_kyc_methods/'.$result->id) }}" class="form-horizontal">
                    {{ csrf_field() }}
        
                    <div class="box-body">
                        <input type="hidden" name="country" value="{{$result->id}}">
                        
                        <div class="form-group">
                            <div class="col-sm-1"></div>
                            <div class="col-sm-6">
                                <input type="checkbox" id="automatic_kyc" name="automatic_kyc" value="1" @if($result->automatic_kyc == '1') checked @endif>
                                <label for="automatic_kyc"> Automatic KYC</label><br>
                                <input type="checkbox" id="manual_kyc" name="manual_kyc" value="1" @if($result->manual_kyc == '1') checked @endif>
                                <label for="manual_kyc"> Manual KYC</label>
                            </div>
                        </div>
                        
                    </div>
            
                    <div class="box-footer">
                        <a class="btn btn-danger" href="{{ url('admin/settings/country') }}">Cancel</a>
                        <button type="submit" class="btn btn-primary pull-right">&nbsp; Submit &nbsp;</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
@endsection