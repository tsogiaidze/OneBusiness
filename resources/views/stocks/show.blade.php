@extends('layouts.custom')

@section('content')
<section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <div class="row">
              <div class="col-xs-9">
                <h4>Stock Receiving</h4>
              </div>
              <div class="col-xs-3">

              </div>
            </div>
          </div>
        <form class="form-horizontal" action="{{ route('stocks.update', [ $stock ,'corpID' => $corpID]) }}" method="POST" >
            {{ csrf_field() }}
            <input type="hidden" name="corpID" value="{{$corpID}}" >
            <input type="hidden" name="_method" value="PATCH">
          <div class="panel-body" style="margin: 30px 0px;">
            <div class="row" style="margin-bottom: 20px;">
                <div class="form-group">
                  <div class="col-sm-6">
                    <label class="control-label col-sm-2">
                        P.O#:
                      </label>
                      <div class="col-sm-4">
                        <select name="po" id="PO" class="form-control" disabled >
                          <option value=""></option>
                          @foreach($pos as $po)
                            <option value="{{$po->po_no}}" >{{$po->po_no}}</option>
                          @endforeach
                        </select>
                      </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-6">
                    <label class="control-label col-sm-2">
                      D.R#:
                    </label>
                    <div class="col-sm-5">
                      <input type="text" class="form-control" name="RR_No" value="{{$stock->RR_No}}" disabled >
                      <input type="hidden" id="RR_No_hidden" class="form-control" name="RR_No" value="{{$stock->RR_No}}" >
                    </div>

                    <label class="control-label col-sm-1">
                      Date
                    </label>
                    <div class="col-sm-4">
                      <input type="date" class="form-control" name="RcvDate" id="" value="{{$stock->RcvDate->format("Y-m-d")}}" {{ $stock->check_transfered() ? "disabled" : "" }} >
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-6">
                    <label class="control-label col-sm-2">
                      VENDOR
                    </label>
                    <div class="col-sm-10">
                      <select name="Supp_ID" class="form-control" {{ $stock->check_transfered() ? "disabled" : "" }} >
                        <option value=""></option>
                        @foreach($vendors as $vendor)
                          <option {{ $stock->Supp_ID == $vendor->Supp_ID ? "selected" : "" }}  value="{{$vendor->Supp_ID}}">{{$vendor->VendorName}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <a class="pull-right btn btn-success {{ \Auth::user()->checkAccessByIdForCorp($corpID, 35, 'A') ? "" : "disabled" }} " id="pressF2" {{ $stock->check_transfered() ? "disabled" : "" }} >
                    Add Row
                    <br>
                    (F2)
                    </a>
                  </div>
                </div>
                
            </div>
            <div class="table-responsive">
              <table id="table_editable" class="table table-bordered" style="width: 100% !important; dispaly: table;" >
                <tbody>
                  <tr>
                    <th style="max-width: 100px;margin: 0px;box-sizing: border-box;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;">Item Code</th>
                    <th>Product Line</th>
                    <th>Brand</th>
                    <th>Description</th>
                    <th>Cost/Unit</th>
                    <th>Served</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th>Unit</th>
                    <th style="min-width: 100px;">Action</th>
                  </tr>
                  @if( $stock_details->count() > 0 )
                    @foreach($stock_details as $detail)
                      <tr class="editable" data-id="{{$detail->Movement_ID}}">
                        <input type="hidden" name="type[{{$detail->Movement_ID}}]" class="input_type" value="none">
                        <td class="edit_ItemCode"  data-field="ItemCode" >
                          <span class="value_ItemCode">{{$detail->stock_item ? $detail->stock_item->ItemCode : ""}}</span>
                          <input type="hidden" class="input_old_item_id" name="old_item_id[{{$detail->Movement_ID}}]" value="{{$detail->item_id}}" >
                          <input type="hidden" class="input_item_id" name="item_id[{{$detail->Movement_ID}}]" value="{{$detail->item_id}}" >
                          <input autocomplete="off" class="show_suggest input_ItemCode" type="hidden" name="ItemCode[{{$detail->Movement_ID}}]" id="" value="{{$detail->stock_item ? $detail->stock_item->ItemCode : ""}}" >
                        </td>
                        <td class="edit_Prod_Line" data-field="Prod_Line" >
                          <span class="value_Prod_Line">{{$detail->stock_item ? $detail->stock_item->product_line->Product : ""}}</span>
                          <input autocomplete="off" class="show_suggest input_Prod_Line" type="hidden"  name="Prod_Line[{{$detail->Movement_ID}}]" value="{{$detail->stock_item ? $detail->stock_item->product_line->Product : ""}}" >
                        </td>
                        <td class="edit_Brand" data-field="Brand" >
                          <span class="value_Brand">{{$detail->stock_item ? $detail->stock_item->brand->Brand : ""}}</span>
                          <input autocomplete="off" class="show_suggest input_Brand" type="hidden" name="Brand[{{$detail->Movement_ID}}]" id="" value="{{$detail->stock_item ? $detail->stock_item->brand->Brand : ""}}" >
                        </td>
                        <td class="edit_Description" >
                          <span class="value_Description" >{{$detail->stock_item ? $detail->stock_item->Description : ""}}</span>
                        </td>
                        <td class="edit_Cost text-right" data-field="Cost" >
                        <span class="value_Cost">{{number_format($detail->Cost,2)}}</span>
                          <input type="hidden" data-validation-error-msg="Invalid input: Please enter a number."  data-validation="number" data-validation-allowing="float"  data-validation-optional="true" class="input_Cost" name="Cost[{{$detail->Movement_ID}}]" id="" value="{{ str_replace( ',', '', number_format($detail->Cost,2) ) }}" >
                        </td>
                        <td class="edit_ServedQty text-right" >
                          <span class="value_ServedQty">{{$detail->ServedQty }}</span>
                        </td>
                        <td class="edit_Qty text-right" data-field="Qty" >
                          <span class="value_Qty">{{$detail->Qty }}</span>
                          <input type="hidden" data-validation-error-msg="Invalid input: Please enter a number."  data-validation="number" data-validation-allowing="float" data-validation-optional="true" class="input_Qty" name="Qty[{{$detail->Movement_ID}}]" id="" value="{{$detail->Qty }}" >
                        </td>
                        <td class="edit_Sub text-right">
                          <span class="value_Sub">{{ number_format( $detail->Cost * $detail->Qty , 2) }}</span>
                          <input type="hidden"  data-validation-error-msg="Invalid input: Please enter a number."  data-validation="number" data-validation-allowing="float" data-validation-optional="true" class="input_Sub" name="Sub[{{$detail->Movement_ID}}]" id="" value="{{ str_replace(',', '', number_format( $detail->Cost * $detail->Qty , 2)) }} " >
                        </td>
                        <td class="edit_Unit" >
                          <span class="value_Unit">{{$detail->stock_item ? $detail->stock_item->Unit : ""}}</span>
                        </td>
                        <td class="text-center" >
                          <a class="btn btn-primary edit {{ \Auth::user()->checkAccessByIdForCorp($corpID, 35, 'E') ? "" : "disabled" }} " {{ $stock->check_transfered() ? "disabled" : "" }}>
                            <i class="fas fa-pencil-alt"></i>
                          </a>
                          <a href="{{route('stocks.delete_detail', [ $stock , $detail , 'corpID' => $corpID] )}}" class="delete_row btn btn-danger {{ \Auth::user()->checkAccessByIdForCorp($corpID, 35, 'D') ? "" : "disabled" }} " {{ $stock->check_transfered() ? "disabled" : "" }}>
                            <i class="fa fa-trash"></i>
                          </a>
                        </td>
                      </tr>
                    @endforeach
                  @else
                    <tr id="no-item">
                      <td colspan="10" style="color: red;" class="text-center" >
                        No items
                      </td>
                    </tr>
                  @endif

                  <tr class="editable" id="example" data-id="" style="display: none;">
                    <input type="hidden" name="add_type[]" class="input_type" value="add">
                    <td class="edit_ItemCode"  data-field="ItemCode" >
                      <span class="value_ItemCode"></span>
                      <input class="input_old_item_id" type="hidden" name="add_old_item_id[]" value="" >
                      <input class="input_item_id" type="hidden" name="add_item_id[]" value="" >
                      <input autocomplete="off" class="show_suggest input_ItemCode" type="hidden" name="add_ItemCode[]" id="" value="" >
                    </td>
                    <td class="edit_Prod_Line" data-field="Prod_Line" >
                      <span class="value_Prod_Line"></span>
                      <input autocomplete="off" class="show_suggest input_Prod_Line" type="hidden" name="add_Prod_Line[]" value="" >
                    </td>
                    <td class="edit_Brand" data-field="Brand" >
                      <span class="value_Brand"></span>
                      <input autocomplete="off" class="show_suggest input_Brand" type="hidden" name="add_Brand[]" id="" value="" >
                    </td>
                    <td class="edit_Description" >
                      <span class="value_Description"></span>
                    </td>
                    <td class="edit_Cost text-right" data-field="Cost" >
                    <span class="value_Cost"></span>
                      <input type="hidden" data-validation-error-msg="Invalid input: Please enter a number." data-validation="number" data-validation-allowing="float"  data-validation-optional="true" class="input_Cost" name="add_Cost[]" id="" value="" >
                    </td>
                    <td class="edit_ServedQty text-right" >
                      <span class="value_ServedQty"></span>
                    </td>
                    <td class="edit_Qty text-right" data-field="Qty" >
                      <span class="value_Qty"></span>
                      <input type="hidden" data-validation-error-msg="Invalid input: Please enter a number."  data-validation="number" data-validation-allowing="float" data-validation-optional="true" class="input_Qty" name="add_Qty[]" id="" value="" >
                    </td>
                    <td class="edit_Sub text-right" >
                      <span class="value_Sub"></span>
                      <input type="hidden" data-validation-error-msg="Invalid input: Please enter a number."  data-validation="number" data-validation-allowing="float" data-validation-optional="true" class="input_Sub" name="add_Sub[]" id="" value="" >
                    </td>
                    <td class="edit_Unit" >
                      <span class="value_Unit"></span>
                    </td>
                    <td class="text-center" >
                      <a class="btn btn-primary edit {{ \Auth::user()->checkAccessByIdForCorp($corpID, 35, 'E') ? "" : "disabled" }} " {{ $stock->check_transfered() ? "disabled" : "" }}>
                        <i class="fas fa-pencil-alt"></i>
                      </a>
                      <a href="#" class="delete_row btn btn-danger {{ \Auth::user()->checkAccessByIdForCorp($corpID, 35, 'D') ? "" : "disabled" }} " {{ $stock->check_transfered() ? "disabled" : "" }}>
                        <i class="fa fa-trash"></i>
                      </a>
                    </td>
                  </tr>

                  <tr class="" id="add-row" style="display: none;">
                    <input type="hidden" name="item_id" value="" class="input_item_id">
                    <td> <input autocomplete="off" type="text" name="ItemCode" class="form-control check_focus input_ItemCode"> </td>
                    <td> <input autocomplete="off" type="text" name="Prod_Line" class="form-control check_focus input_Prod_Line"> </td>
                    <td> <input autocomplete="off" type="text" name="Brand" class="form-control check_focus input_Brand"> </td>
                    <td> <input type="text" name="Description" id="" class="form-control input_Description"> </td>
                    <td> <input type="text" data-validation-error-msg="Invalid input: Please enter a number."  data-validation="number" data-validation-allowing="float" data-validation-optional="true" name="Cost" id="" class="form-control input_Cost"> </td>
                    <td>0</td>
                    <td> <input type="text" data-validation-error-msg="Invalid input: Please enter a number."  data-validation="number" data-validation-allowing="float" data-validation-optional="true" name="Qty" id="" value="1" class="input_Qty form-control"> </td>
                    <td> <input type="text" data-validation-error-msg="Invalid input: Please enter a number."  data-validation="number" data-validation-allowing="float" data-validation-optional="true" name="Sub" id="" class="input_Sub form-control"> </td>
                    <td class="input_Unit" ></td>
                    <td class="text-center" >
                      <a data-href="#"  class="btn btn-primary add_detail {{ \Auth::user()->checkAccessByIdForCorp($corpID, 35, 'A') ? "" : "disabled" }}" >
                        <i class="fa fa-check"></i>
                      </a>
                      <a data-href="#"  class="btn btn-danger delete_add_detail {{ \Auth::user()->checkAccessByIdForCorp($corpID, 35, 'D') ? "" : "disabled" }}" >
                        <i class="fa fa-trash"></i>
                      </a>
                    </td>
                  </tr>

                </tbody>
              </table>
            </div>


            <table class="table table-bordered" id="recommend-table" style=" display: none; " >
              <thead>
                <tr style="display:table;width:99%;table-layout:fixed; background:  #f27b82" >
                  <th>Item Code</th>
                  <th>Product Line</th>
                  <th>Brand</th>
                  <th>Description</th>
                  <th>Unit</th>
                  <th>Unit Cost</th>
                </tr>
              </thead>
              <tbody style="display:block; max-height:300px; overflow-y:scroll; background: #f4b2b6;">
                @foreach($stockitems as $stockitem )
                  <tr class="recommend_row" style="display:table;width:100%;table-layout:fixed;" >
                    <td class="recommend_item_id" style="display: none;">{{$stockitem->item_id}} </td>
                    <td class="recommend_itemcode" >{{$stockitem->ItemCode}}</td>
                    <td class="recommend_prod_line" >{{$stockitem->product_line->Product}} </td>
                    <td class="recommend_prod_line_id" style="display: none;" >{{$stockitem->Prod_Line}} </td>
                    <td class="recommend_brand"  >{{$stockitem->brand->Brand}}</td>
                    <td class="recommend_brand_id" style="display: none;" >{{$stockitem->Brand_ID}}</td>
                    <td class="recommend_description">{{$stockitem->Description}}</td>
                    <td class="recommend_unit">{{$stockitem->Unit}}</td>
                    <td class="recommend_cost">{{$stockitem->LastCost}}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>

            <div class="row" style="margin-top: 200px;">
              <div class="col-sm-3 pull-right">
                <h4>
                  <strong>TOTAL AMOUNT:</strong>
                  <span id="total_amount" style="color:red">{{ number_format($stock->total_amount() , 2) }}</span>
                  <input type="hidden" name="total_amt" id="total_amt" value="{{ number_format($stock->total_amount() , 2) }}" >
                </h4>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <a type="button" class="btn btn-default" href="{{  $_COOKIE['last_index_url'] }}">
                  <i class="fa fa-reply"></i> Back
                </a>
              </div>
              <div class="col-md-6">
                <button type="button" data-toggle="modal" class="btn btn-success pull-right save_button {{ \Auth::user()->checkAccessByIdForCorp($corpID, 35, 'A') ? "" : "disabled" }} " {{ $stock->check_transfered() ? "disabled" : "" }}>
                  Save
                </button>
              </div>
            </div>

                <!-- Modal alert -->
                <div class="modal fade" id="confirm_save" role="dialog">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">
                          <strong>Confirm Save</strong>
                        </h4>
                      </div>
                      <div class="modal-body">
                        <p> Are you sure you want to save? </p>
                        <div class="checkbox">
                          <label> <input type="checkbox" name="PrintRR" id=""> Print RR Stub </label>
                        </div>
                      </div>
                      <div class="modal-footer" style="margin-top: 100px;">
                        <div class="col-md-6">
                          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                            <i class="fa fa-reply"></i> Back  
                          </button>
                        </div>
                        <div class="col-md-6">
                          <button class="btn btn-primary" id="submit-form" type="submit">Save</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- End modal alert -->


          </div>
        </form>
          
        </div>
      </div>
    </div>
</section>

<!-- Modal alert -->
<div class="modal fade" id="alert" role="dialog" >
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">
          <strong>EDIT DR</strong>
        </h4>
      </div>
      <div class="modal-body">
        <p>Some or all of the items on this DR have been transferred already. You cannot edit or delete this anymore...</p>
      </div>
      <div class="modal-footer" style="margin-top: 100px;">
        <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal alert -->
@if($print)
  <div class="print-section">
    <p>
      SSR #: {{ $stock->txn_no }} <br>
      Date/Time: {{ $stock->DateSaved->format('m-d-Y h:i:s A') }}
    </p>
    <p>
      PO <br>
      {{ $stock->vendor->VendorName }} <br>
      {{ $stock->RR_No }}
    </p>
    <table class="table">
      <tbody>
      @php $totalCost = 0 @endphp
      @foreach($stock_details as $detail)
        @php $totalCost += $detail->Cost * $detail->Qty @endphp
        <tr>
          <td>{{ $detail->stock_item->ItemCode }}</td>
          <td>{{ $detail->Qty }}</td>
          <td>
            {{ number_format($detail->Cost, 2) }}<br>
            {{ number_format($detail->stock_item->LastCost, 2) }}
          </td>
          <td>
            {{ number_format($detail->Cost * $detail->Qty, 2) }} <br>
            @if($detail->Cost != $detail->stock_item->LastCost)
            {{ $detail->stock_item->Cost }}
              {{ number_format(($detail->Cost - $detail->stock_item->LastCost) / $detail->stock_item->LastCost * 100, 2) }}%
            @else
              0.00%
            @endif
          </td>
        </tr>
      @endforeach
      <tr>
        <td colspan="2"></td>
        <td> <i>TOTAL:</i></td>
        <td><i>{{ number_format($totalCost, 2) }}</i></td>
      </tr>
      </tbody>
    </table>
    <p>
      By: {{ \Auth::user()->UserName }}
    </p> 
  </div>
@endif

@endsection

@section('pageJS')
  @if($print)
  <script type="text/javascript">
    window.print();
  </script>
  @endif

  <script>

    $.validate({
      form : 'form'
    });

    $('form').on('keypress', function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });

    var old_total = parseFloat($('#total_amount').text().replace(",", ""));
  
    function refresh_sub()
    {
      var sub = 0;
      $( ".editable" ).each(function() {
        if(!$(this).is(':hidden') && $(this).find('.input_Sub').val() != '' )
        {
          sub += parseFloat($(this).find('.input_Sub').val().replace(',', '') );
        }
      });

      $( "#add-row" ).each(function() {
        if(!$(this).is(':hidden') && $(this).find('.input_Sub').val() != '' )
        {
          sub += parseFloat($(this).find('.input_Sub').val().replace(',', '') );
        }
      });

      $('#total_amount').text(sub.numberFormat(2));
      $('#total_amt').val(sub);
    }

    function update_old_sub()
    {
      var sub = 0;
      $( ".editable" ).each(function() {
        if(!$(this).is(':hidden'))
        {
          sub += parseFloat($(this).find('.input_Sub').val());
        }
      });
      old_total = sub;
    }

    $('body').on('click', '.delete_row', function(event)
    {
      event.preventDefault();
      $self = $(this);
      if($self.parents('.editable').find('.input_type').val() == 'add')
      {
        $self.parents('.editable').remove();
      }
      else
      {
        $self.parents('.editable').find('.input_type').val('deleted');
        $self.parents('.editable').css('display', 'none');
      }
      refresh_sub();
      update_old_sub();
    });

    $('#submit-form').on('click', function(event)
    {
      event.preventDefault();
      $('#add-row').remove();
      $('#example').remove();
      $('#total_amt').val($('#total_amount').text());
      if($('input[name="PrintRR"]:checked').length > 0) {
        $('form').attr('action', $('form').attr('action') + "&print=true");
      }
      $('form').submit();
    })

    $('.add_detail').on('click', function()
    {
      if( !$('.input_Cost ').hasClass('error') && !$('.input_Qty ').hasClass('error') && !$('.input_Sub ').hasClass('error') )
      {
      var $add_row = $('#add-row');
      var new_element = $('#example').clone();
      $.validate({
        form : 'form'
      });

      $trParent = $(this).parents('tr');
      $trParent.find('td:eq(0) .error').remove();
      if($trParent.find('input[name="item_id"]').val() == "") {
        $trParent.find('td:eq(0)').append("<span class='error'>Please select an item.</span>");
        return;
      }

      $('#recommend-table').css('display', "none");
      
      new_element.css("display", "").removeAttr('id');

      $('.editable').last().after(new_element);
      
      //ItemCode
      new_element.find('.input_item_id').val($add_row.find('.input_item_id').val());
      new_element.find('.input_ItemCode').val($add_row.find('.input_ItemCode').val());
      new_element.find('.value_ItemCode').text($add_row.find('.input_ItemCode').val());
      
      //ProductLine
      new_element.find('.value_Prod_Line').text($add_row.find('.input_Prod_Line').val());
      new_element.find('.input_Prod_Line').val($add_row.find('.input_Prod_Line').val());

      //Brand
      new_element.find('.value_Brand').text($add_row.find('.input_Brand').val());
      new_element.find('.input_Brand').val($add_row.find('.input_Brand').val());

      //Description
      new_element.find('.value_Description').text($add_row.find('.input_Description').val());

      //Cost/Unit
      if ($add_row.find('.input_Cost').val() != '')
      {
        new_element.find('.value_Cost').text( parseFloat($add_row.find('.input_Cost').val()).toFixed(2) );
        new_element.find('.input_Cost').val( parseFloat($add_row.find('.input_Cost').val()).toFixed(2) );
      }
      else
      {
        new_element.find('.value_Cost').text('0.00');
        new_element.find('.input_Cost').val('0.00');
      }

      //Served
      new_element.find('.value_ServedQty').text("0");
      
      //Qty
      if($add_row.find('.input_Qty').val() != '')
      {
        new_element.find('.value_Qty').text( parseInt($add_row.find('.input_Qty').val()) );
        new_element.find('.input_Qty').val( parseInt($add_row.find('.input_Qty').val()) );
      }
      else
      {
        new_element.find('.value_Qty').text( '0.00' );
        new_element.find('.input_Qty').val( '0.00' );
      }

      
      //Subtotal
      if( $add_row.find('.input_Sub').val() != '' )
      {
        new_element.find('.value_Sub').text( parseFloat($add_row.find('.input_Sub').val()).toFixed(2) );
        new_element.find('.input_Sub').val( parseFloat($add_row.find('.input_Sub').val()).toFixed(2) );
      }
      else
      {
        new_element.find('.value_Sub').text( '0.00' );
        new_element.find('.input_Sub').val( '0.00' );
      }

      //Unit
      new_element.find('.value_Unit').text($add_row.find('.input_Unit').text());


      $('#add-row').find('input').val('');
      $('#add-row').find('.input_Qty').val('1');
      $('#add-row').find('.input_Unit').text('');
      $('#add-row').css('display', 'none');
      $('.recommend_row').removeClass('row-highlight');
      
      //reupdate sub total
      update_old_sub();
      refresh_sub();
      }
    });

    @if($stock->check_transfered())
      $(window).on('load',function(){
        $('#alert').modal('show');
      });
    @endif

    $('body').on('focus', '.editable input' , function()
    {
      $('.last_focus').removeClass('last_focus');
      $(this).addClass('last_focus');
    }
    );

    $('body').on('focus', '.check_focus' , function()
    {
      $('.last_focus').removeClass('last_focus');
      $(this).addClass('last_focus');
    }
    );

    $('.show_suggest').on('change paste keyup', function()
    {
      if($(this).val() == "")
      {
        $('#recommend-table').css('display', "");
        $('.recommend_row').css('display', "table");
        // $('.recommend_row').removeClass('row-highlight');
      }
    });


    $('.recommend_row').click(function(){
      $('.recommend_row').removeClass('row-highlight');
      if($('.last_focus').hasClass('check_focus'))
      {
        $parent = $('.last_focus').parents('#add-row');
        $(this).addClass('row-highlight');
        $('#add-row').find('.input_ItemCode').val($(this).find('.recommend_itemcode').text());
        $('#add-row').find('.input_Prod_Line').val($(this).find('.recommend_prod_line').text());
        $('#add-row').find('.input_Brand').val($(this).find('.recommend_brand').text());
        $('#add-row').find('.input_Description').val($(this).find('.recommend_description').text());
        $('#add-row').find('.input_Cost').val($(this).find('.recommend_cost').text());
        if($(this).find('.recommend_cost').text() != "")
        {
          $('#add-row').find('.input_Cost').val($(this).find('.recommend_cost').text());
          if( ($('#add-row').find('.input_Cost').val() != "" ) && ($('#add-row').find('.input_Qty').val() != "" ) )
          {
            var val = parseFloat($parent.find('.input_Cost').val()) * parseFloat($parent.find('.input_Qty').val());
            $('#add-row').find('.input_Sub').val(val);
          }
        }
        $parent.find('.input_Unit').text($(this).find('.recommend_unit').text());
        $parent.find('.input_item_id').val($(this).find('.recommend_item_id').text());
        $('#recommend-table').css('display', "none");
      }
      else
      {
        $parent = $('.last_focus').parents('.editable');
        $(this).addClass('row-highlight');
        $('.last_focus').parents('.editable').find('.edit_ItemCode').find(".input_ItemCode").val($(this).find('.recommend_itemcode').text());
        $('.last_focus').parents('.editable').find('.edit_ItemCode').find(".input_item_id").val($(this).find('.recommend_item_id').text());
        $('.last_focus').parents('.editable').find('.edit_Brand').find(".input_Brand").val($(this).find('.recommend_brand').text());
        $('.last_focus').parents('.editable').find('.edit_Prod_Line').find(".input_Prod_Line").val($(this).find('.recommend_prod_line').text());
        $('.last_focus').parents('.editable').find('.edit_Description').find(".value_Description").text($(this).find('.recommend_description').text());
        $('.last_focus').parents('.editable').find('.edit_Unit').find(".value_Unit").text($(this).find('.recommend_unit').text());
        $('.last_focus').parents('.editable').find('.edit_ItemCode').find(".input_Cost").val($(this).find('.recommend_cost').text());
        
        $('#recommend-table').css('display', "none");
        if($(this).find('.recommend_cost').text() != "")
        {
          $parent.find('.input_Cost').val($(this).find('.recommend_cost').text());
          if( ($parent.find('.input_Cost').val() != "" ) && ($parent.find('.input_Qty').val() != "" ) )
          {
            var val = parseFloat( $parent.find('.input_Cost').val() ) * parseFloat($parent.find('.input_Qty').val());
            $parent.find('.input_Sub').val(val);
          }
        }
      }
      refresh_sub();
    });

    $('body').on('click', '.edit', function(){
      var self = $(this);
      $.validate({
        form : 'form'
      });

      $('#recommend-table').css('display', "none");

      if($(this).find('i').hasClass('fa-pencil'))
      {
        $(this).find('i').removeClass('fa-pencil').addClass('fa-save');
        $(this).parents('.editable').find( ".input_ItemCode" ).val($(this).parents('.editable').find('.value_ItemCode').text()).attr("type", "text") ;
        $(this).parents('.editable').find( ".input_Prod_Line" ).val($(this).parents('.editable').find('.value_Prod_Line').text()).attr("type", "text") ;
        $(this).parents('.editable').find( ".input_Brand" ).val($(this).parents('.editable').find('.value_Brand').text()).attr("type", "text") ;
        $(this).parents('.editable').find( ".input_Qty" ).val($(this).parents('.editable').find('.value_Qty').text()).attr("type", "text") ;
        $(this).parents('.editable').find( ".input_Cost" ).val($(this).parents('.editable').find('.value_Cost').text().replace(',', '')).attr("type", "text") ;
        $(this).parents('.editable').find( ".input_Sub" ).val($(this).parents('.editable').find('.value_Sub').text().replace(',', '')).attr("type", "text") ;
        $(this).parents('.editable').find( ".input_type" ).val('editting') ;
        $(this).parents('.editable').find('.value_ItemCode, .value_Prod_Line, .value_Brand, .value_Qty, .value_Cost, .value_Sub').text("");
      }
      else
      {
        $(this).parents('tr').find('.error').remove();
        if( !$('.input_Cost ').hasClass('error') && !$('.input_Qty ').hasClass('error') && !$('.input_Sub ').hasClass('error') )
        {
        self.parents('.editable').find('.value_ItemCode').text(self.parents('.editable').find('.input_ItemCode').val() );
        self.parents('.editable').find('.value_Prod_Line').text(self.parents('.editable').find('.input_Prod_Line').val() );
        self.parents('.editable').find('.value_Brand').text(self.parents('.editable').find('.input_Brand').val());

        if( self.parents('.editable').find('.input_Cost').val() != "" )
        {
          self.parents('.editable').find('.value_Cost').text( parseFloat(self.parents('.editable').find('.input_Cost').val()).toFixed(2) );
        }
        else
        {
          self.parents('.editable').find('.value_Cost').text('0.00');
        }

        if( self.parents('.editable').find('.input_Qty').val() != "" )
        {
          self.parents('.editable').find('.value_Qty').text( parseInt(self.parents('.editable').find('.input_Qty').val()) );
        }
        else
        {
          self.parents('.editable').find('.value_Qty').text('0');
        }

        if( self.parents('.editable').find('.input_Sub').val() != "" )
        {
          self.parents('.editable').find('.value_Sub').text(parseFloat(self.parents('.editable').find('.input_Sub').val()).toFixed(2));
        }
        else
        {
          self.parents('.editable').find('.value_Sub').text('0.00');
        }

        self.parents('.editable').find( ".input_type" ).val('none') ;
        
        $('#recommend-table').css('display', "none");

        self.find('i').addClass('fa-pencil').removeClass('fa-save');
        
        self.parents('.editable').find( ".input_ItemCode" ).attr("type", "hidden");
        self.parents('.editable').find( ".input_Cost" ).attr("type", "hidden");
        self.parents('.editable').find( ".input_Prod_Line" ).attr("type", "hidden");
        self.parents('.editable').find( ".input_Brand" ).attr("type", "hidden");
        self.parents('.editable').find( ".input_Qty" ).attr("type", "hidden");
        self.parents('.editable').find( ".input_Sub" ).attr("type", "hidden");
        
        }
      }
      
    });

    @if(!$stock->check_transfered())
      $(document).keydown(function(e) {
        if(e.which == 113) {
          $('#add-row').css('display' , ''); 
          $('#no-item').css('display', 'none');
          return false;
        }
      });
    @endif

    $('#pressF2').click(function(){
      $('#add-row').css('display' , ''); 
      $('#no-item').css('display', 'none');
    });

    $('body').on('click', '.input_ItemCode ,.input_Prod_Line, .input_Brand', function(){
      $('#recommend-table').css('display', "");
    });

    $('body').on( 'change paste keyup', '.input_Cost ,.input_Qty, .input_Sub', function()
    {
      
      $self = $(this);
      if ($self.parents('#add-row').length) 
      {
        $parent = $self.parents('#add-row');
      }
      else
      {
        $parent = $self.parents('.editable');
      }
      if( ($parent.find('.input_Cost').val().match(/^-?\d+(?:[.]\d*?)?$/)  || $parent.find('.input_Cost').val() == "" ) && 
      ($parent.find('.input_Qty').val().match(/^-?\d+(?:[.]\d*?)?$/)  || $parent.find('.input_Qty').val() == "" ) &&
      ($parent.find('.input_Sub').val().match(/^-?\d+(?:[.]\d*?)?$/)  || $parent.find('.input_Sub').val() == "" ) )
      {
        if ($self.hasClass('input_Cost') || $self.hasClass('input_Qty'))
        {
          if( ($parent.find('.input_Cost').val() != "" ) && ($parent.find('.input_Qty').val() != "" ) )
          {
            var val = parseFloat($parent.find('.input_Cost').val()) * parseFloat($parent.find('.input_Qty').val());
            $parent.find('.input_Sub').val(val);
          }
          else
          {
            $parent.find('.input_Sub').val('');
            if( $self.hasClass('input_Cost') )
            {
              $parent.find('.input_Sub').val('0');
            }
            else
            {
              $parent.find('.input_Sub').val('0');
            }
          }
        }

        if ($self.hasClass('input_Sub'))
        {
          if( ($parent.find('.input_Cost').val() != "" ) && ($parent.find('.input_Sub').val() != "" ) )
          {
            var val = parseFloat($parent.find('.input_Sub').val()) / parseFloat($parent.find('.input_Qty').val());
            $parent.find('.input_Cost').val(val);
          }
          else
          {
          }

          if($parent.find('.input_Sub').val() != "" )
          {
            var newtotal = (old_total + parseFloat( $parent.find('.input_Sub').val() ));
          }
          else
          {
            $parent.find('.input_Cost').val('0');
          }
        }
        refresh_sub();
      }
      else
      {
      }
    });

    var ignore_key = false;
    var key_next = false;
    var key_prev = false;
    var key_enter = false;
    var last;
    var index;
    $('body').on('keydown', '.input_ItemCode ,.input_Prod_Line, .input_Brand', function(e){      
      if(e.which == 40) {
        key_prev = true;
        ignore_key = true;
      }

      if(e.which == 38) {
        key_next = true;
        ignore_key = true;
      }
    }).on( 'click change paste keyup', '.input_ItemCode ,.input_Prod_Line, .input_Brand' ,function(e){
        if(ignore_key || (e.which == 13)){
        if(key_prev)
        {
          last = $('.row-highlight');
          index = $('.recommend_row:not(:hidden)').index(last);
          if (last[0] == $('.recommend_row:not(:hidden)').last()[0])
          {
            $('.recommend_row:not(:hidden):eq(0)').addClass('row-highlight');
          }
          else
          {
            $(".recommend_row:not(:hidden):eq("+(index+1)+")").addClass('row-highlight');
          }
          last.removeClass('row-highlight');
          $('.row-highlight')[0].scrollIntoView({
            behavior: "smooth",
            block: "center" 
          });
        }

        if(key_next)
        {
          last = $('.row-highlight');
          index = $('.recommend_row:not(:hidden)').index(last);
          if (last[0] == $('.recommend_row:not(:hidden)').first()[0])
          {
            $('.recommend_row:not(:hidden)').last().addClass('row-highlight');
          }
          else
          {
            $(".recommend_row:not(:hidden):eq("+(index-1)+")").addClass('row-highlight');
          }
          last.removeClass('row-highlight'); 
          $('.row-highlight')[0].scrollIntoView({
            behavior: "smooth",
            block: "center" 
          }); 
        }

        if(e.which == 13)
        {
          $('.row-highlight').click();
          $('body').focus();
        }
        ignore_key = false;
        key_next = false;
        key_prev = false;
        key_enter = false;

        return false;
      }
      $('.recommend_row').css('display', 'table');
      $self = $(this);

      if ($self.parents('#add-row').length) 
      {
        $parent = $self.parents('#add-row');
      }
      else
      {
        $parent = $self.parents('.editable');
      }
      
      if ($self.hasClass('input_ItemCode'))
      {
        $('.recommend_row').each(function()
        {
          if ( $(this).find('.recommend_itemcode').text().toUpperCase().includes( $parent.find('.input_ItemCode').val().toUpperCase() ) ) 
          
          {
          }
          else
          {
            $(this).css('display' , 'none');
          }
        });
      }

      if(($self.hasClass('input_Brand')))
      {
        $('.recommend_row').each(function()
        {
          if ( $(this).find('.recommend_brand').text().toUpperCase().includes( $parent.find(".input_Brand").val().toUpperCase() ) )
          {
          }
          else
          {
            $(this).css('display' , 'none');
          }
        });
      }

      if($self.hasClass('input_Prod_Line'))
      {
        $('.recommend_row').each(function()
        {
          if ( $(this).find('.recommend_prod_line').text().toUpperCase().includes( $parent.find(".input_Prod_Line").val().toUpperCase() )  )
          {
          }
          else
          {
            $(this).css('display' , 'none');
          }
        });
      }
      $('.recommend_row').removeClass('row-highlight');
      $('.recommend_row:not(:hidden)').first().addClass('row-highlight');
    });

    $('.delete_add_detail').on('click', function()
    {
      $('#add-row').css('display' , 'none'); 
      $('.recommend_row').removeClass('row-highlight');
      $('#add-row .input_ItemCode').val('');
      $('#add-row .input_Prod_Line').val('');
      $('#add-row .input_Brand').val('');
      $('#add-row .input_Description').val('');
      $('#add-row .input_Cost').val('');
      $('#add-row .input_Unit').text('');
      $('#add-row .input_item_id').val('');
      $('#recommend-table').css('display', "none");
      refresh_sub();
    });

    $('.save_button').on('click', function(event)
    {
      $('#table_editable td span.error').remove();
      $('#table_editable input[value="editting"]').each(function() {
        $(this).parents('.editable').find('td:eq(0)').append("<span class='error'>Please save or delete this row first…</span>");
      });

      if($('#add-row').is(':visible')) {
        $('#add-row').find('td:eq(0)').append("<span class='error'>Please save or delete this row first…</span>");
      }

      if($('#table_editable input[value="editting"]').length > 0 || $('#add-row').is(':visible')) {
        return;
      }

      if( $('form').isValid(false) )
      {
        $('#confirm_save').modal('show');
      }
    });

  </script>
@endsection