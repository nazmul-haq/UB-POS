
<div class="span6 toppad">
	<div class="panel panel-info">
		<div class="panel-body">
		  <div class="row">
			<div class="span1" align="center"> {{ HTML::image('images/default.jpg', 'title', array('class' => 'img-circle')) }}</div>
			<div class="span5">
			  <h2 style="text-decoration:underline; margin:0 15px 10px;">{{$customer_info->full_name}}</h2>
			  <table style="margin-left: 15px;" class="table-cus table-user-information">
				<tbody>
				   <tr>
					<td><strong>Customer Id</strong></td>
					<td>:</td>
					<td>{{$customer_info->cus_card_id}}</td>
				  </tr>
				  <tr>
					<td><strong>Customer Type Name</strong></td>
					<td>:</td>
					<td>{{$customer_info->cus_type_name}}</td>
				  </tr>
				  <tr>
					<td><strong>Point</strong></td>
					<td>:</td>
					<td>{{$customer_info->point}}</td>
				  </tr>

				  <tr>
					<td><strong>Taka Per Point</strong></td>
					<td>:</td>
					<td>{{$customer_info->point_unit}}</td>
				  </tr>
				  
				  <tr>
					<td><strong>Discount Percent</strong></td>
					<td>:</td>
					<td>{{$customer_info->discount_percent}}</td>
				  </tr>
				  
				  <tr>
					<td><strong>Advance Payment</strong></td>
					<td>:</td>
					<td>{{$customer_info->advance_payment}}</td>
				  </tr>
				  <tr>
					<td><strong>Due</strong></td>
					<td>:</td>
					<td>{{$customer_info->due}}</td>
				  </tr>

				  <tr>
					<td><strong>User Name</strong></td>
					<td>:</td>
					<td>{{$customer_info->user_name}}</td>
				  </tr>

				  <tr>
					<td><strong>Mobile</strong></td>
					<td>:</td>
					<td>{{$customer_info->mobile}}</td>
				  </tr>
				  <tr>
					<td><strong>Email</strong></td>
					<td>:</td>
					<td><a href="{{$customer_info->email}}">{{$customer_info->email}}</a></td>

				  </tr>
				  <tr>
					<td><strong>Permanent Address</strong></td>
					<td>:</td>
					<td>{{$customer_info->permanent_address}}</td>
				  </tr>
				  <tr>
					<td><strong>Present Address</strong></td>
					<td>:</td>
					<td>{{$customer_info->present_address}}</td>
				  </tr>
				  <tr>
					<td><strong>National Id</strong></td>
					<td>:</td>
					<td>{{$customer_info->national_id}}</td>
				  </tr>
				  

				</tbody>
			  </table>


			</div>
		  </div>
		</div>
	</div>
</div>

