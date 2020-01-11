
<div class="span5 toppad">
	<div class="panel panel-info">
		<div class="panel-body">
		  <div class="row">
			<div class="span1" align="center"> {{ HTML::image('images/default.jpg', 'title', array('class' => 'img-circle')) }}</div>
			
			<div class="span4">
			  <h2 style="text-decoration:underline; margin-bottom: 10px;">{{$supplier_info->supp_or_comp_name}}</h2>
			  <table class="table-cus table-user-information">
				<tbody>
				  <tr>
					<td><strong>User Name</strong></td>
					<td>:</td>
					<td>{{$supplier_info->user_name}}</td>
				  </tr>
				  <tr>
					<td><strong>Permanent Address</strong></td>
					<td>:</td>
					<td>{{$supplier_info->permanent_address}}</td>
				  </tr>
				  <tr>
					<td><strong>Present Address</strong></td>
					<td>:</td>
					<td>{{$supplier_info->present_address}}</td>
				  </tr>
				  <tr>
					<td><strong>Mobile</strong></td>
					<td>:</td>
					<td>{{$supplier_info->mobile}}</td>
				  </tr>
				  <tr>
					<td><strong>Email</strong></td>
					<td>:</td>
					<td><a href="{{$supplier_info->email}}">{{$supplier_info->email}}</a></td>
				 
				  </tr>
				  <tr>
					<td><strong>Advance Payment</strong></td>
					<td>:</td>
					<td>{{$supplier_info->advance_payment}}</td>
				  </tr>
				  <tr>
					<td><strong>Due</strong></td>
					<td>:</td>
					<td>{{$supplier_info->due}}</td>
				  </tr>
				   
				</tbody>
			  </table>
			</div>
		  </div>
		</div>
	</div>
</div>

