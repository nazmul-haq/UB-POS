@extends('_layouts.default')

@section('content')
<style>
</style>
<div class="row">
    <div class="span12">
		@include('_sessionMessage')
        <div class="employee-btn">

            <div class="btn-group">
                <a  class="btn btn-primary" href="javascript:;" data-toggle="dropdown"><i class="icon-user icon-white"></i> Customer Type</a>
                <a 	class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="javascript:;"><span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="#customerTypeModal" role="button" data-toggle="modal"><i class="icon-plus-sign"></i>&nbsp; Add Customer Type</a></li>
                    <li><a href="#viewCustomerTypeModal" role="button" data-toggle="modal"><i class="icon-zoom-in"></i> View Customer Type</a></li>
                </ul>
            </div>
            &nbsp;&nbsp;
            <a href="#addCustomer" role="button" data-toggle="modal"><button class="btn btn-info"><i class="icon-user"></i>&nbsp; Add New Customer</button></a>

        </div>
		{{ Datatable::table()
				->addColumn('Customer ID','Full Name', 'Username', 'Mobile','Purchase Amount', 'Point', 'Due','Register Date', 'Action')
				->setUrl(route('admin.getMembershipData'))
				->render() }}
    </div>
</div>


<script>
    function getMembership(cus_id, cus_type_id){
        var con=confirm("Are you sure ?");
        if(con){
            $(function(){
                var id= $(this).prev();
                console.log(id);
                $.ajax({
                    url: "getMembership"+"/"+ cus_id +"/"+ cus_type_id,
                    success : function(data){
                        $('.'+cus_id).parent().parent().parent().parent().parent().fadeOut('slow');
                    }
                });
            });
            return true;
        }
        else{
            return false;
        }
    }

</script>


@stop