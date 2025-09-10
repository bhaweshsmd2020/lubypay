<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Print</title>
</head>
    <style>
	   body{ font-family: 'Lato', sans-serif; color:#121212;}

	   hr { border-top:1px solid #f0f0f0;}
	   table { border-collapse:collapse;}
	   .code td{ padding:5px;}

	</style>
<body>
   <div style="width:900px; margin:auto; top:20px; position:relative;">
     <table style="margin-bottom:40px;">
	   <tr>
	     <td>
			<?php if(!empty($companyInfo['value'])): ?>
				<img src='<?php echo e(public_path("/images/logos/".$companyInfo["value"])); ?>' width="288" height="90" alt="Logo"/>
			<?php else: ?>
				<img src="<?php echo e(url('public/uploads/userPic/default-logo.jpg')); ?>" width="288" height="90">
			<?php endif; ?>
	     </td>
	   </tr>
	 </table>

      <table>
	    <tr>
		  <td>
		   <table>
			  <tr>
				<td style="font-size:16px; color:#000000; line-height:25px; font-weight:bold;"><?php echo app('translator')->get('message.dashboard.left-table.deposit.deposited-via'); ?></td>
			  </tr>
			  <tr>
				<td style="font-size:15px; color:#4e5c6e; line-height:22px;"><?php echo e(($transactionDetails->payment_method->name == "Mts") ? "Pay Money" : $transactionDetails->payment_method->name); ?></td>
			  </tr>

			  <br><br>
			  <tr>
				<td style="font-size:16px; color:#000000; line-height:25px; font-weight:bold;"><?php echo app('translator')->get('message.dashboard.left-table.deposit.deposited-to'); ?></td>
			  </tr>
			  <tr>
				<td style="font-size:15px; color:#4e5c6e; line-height:22px;"><?php echo e($transactionDetails->currency->code); ?></td>
			  </tr>
			  <br><br>
			  </table>
		  </td>
		  </tr>

		<tr>
		  <td>
		   
		   <table>
			  <tr>
				<td style="font-size:16px; color:#000000; line-height:25px; font-weight:bold;"><?php echo app('translator')->get('message.dashboard.left-table.transaction-id'); ?></td>
			  </tr>
			  <tr>
				<td style="font-size:15px; color:#4e5c6e; line-height:22px;"><?php echo e($transactionDetails->uuid); ?></td>
			  </tr>
			  <br><br>
			  <tr>
				<td style="font-size:16px; color:#000000; line-height:25px; font-weight:bold;"><?php echo app('translator')->get('message.dashboard.left-table.transaction-date'); ?></td>
			  </tr>
			  <tr>
				<td style="font-size:15px; color:#4e5c6e; line-height:22px;"><?php echo e(dateFormat($transactionDetails->created_at)); ?></td>
			  </tr>

			  <br><br>
			  <tr>
				<td style="font-size:16px; color:#000000; line-height:25px; font-weight:bold;"><?php echo app('translator')->get('message.form.status'); ?></td>
			  </tr>
			  <tr>
				<td style="font-size:15px; color:#4e5c6e; line-height:22px;"><?php echo e((($transactionDetails->status == 'Blocked') ? __("Cancelled") :(($transactionDetails->status == 'Refund') ? __("Refunded"):
								__($transactionDetails->status)))); ?></td>
			  </tr>
		   </table>
		  </td>
		  </tr>

		<tr>
		  <td>
		   
		   <table style="margin-top:20px; width:300px;">
			  <tr>
				<td colspan="2" style="font-size:16px; color:#000000; font-weight:bold;"><?php echo app('translator')->get('message.dashboard.left-table.details'); ?></td>
			  </tr>
			  <tr>
				<td style="font-size:15px; color:#000000;"><?php echo app('translator')->get('message.dashboard.left-table.deposit.deposited-amount'); ?></td>
				<td style="font-size:15px; color:#4e5c6e; text-align:right;"><?php echo e(moneyFormat($transactionDetails->currency->symbol, formatNumber($transactionDetails->subtotal))); ?></td>
			  </tr>

			  <?php if(abs($transactionDetails->total) - abs($transactionDetails->subtotal) > 0): ?>
			  	<tr style="padding-bottom:10px;">
					<td style="font-size:15px; color:#000000;"><?php echo app('translator')->get('message.dashboard.left-table.fee'); ?></td>
					<td style="font-size:15px; color:#4e5c6e; text-align:right;"><?php echo e(moneyFormat($transactionDetails->currency->symbol, formatNumber($transactionDetails->charge_percentage+$transactionDetails->charge_fixed))); ?></td>
				</tr>
			  <?php endif; ?>

			  <tr>
			    <td colspan="2" style="border-top:1px solid #eaeaea; padding-top:0; margin-bottom:3px;"></td>
			  </tr>
			  <tr>
				<td style="font-size:15px; color:#000000; font-weight:bold;"><?php echo app('translator')->get('message.dashboard.left-table.total'); ?></td>
				<td style="font-size:15px; color:#4e5c6e; text-align:right;"><?php echo e(moneyFormat($transactionDetails->currency->symbol, formatNumber($transactionDetails->total))); ?></td>
			  </tr>
			  </table>
		    </td>
		  </tr>
	  </table>
   </div>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\lubypay\resources\views/user_dashboard/deposit/depositPaymentPdf.blade.php ENDPATH**/ ?>