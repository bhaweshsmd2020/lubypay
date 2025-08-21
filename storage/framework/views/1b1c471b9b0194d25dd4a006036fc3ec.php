
<?php $__env->startSection('title', 'Edit Transaction'); ?>

<?php $__env->startSection('head_style'); ?>
    <style type="text/css">
        #crypto_txid, #crypto_sender_address, #crypto_receiver_address, #crypto-sent-status, #crypto-received-status
        {
		    /*Bread word to new line*/
		    word-wrap: break-word !important; /* same as - overflow-wrap: break-word;*/
		}
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>

<?php
    $transaction_details = DB::table('trans_device_info')->where('trans_id', $transaction->id)->first();
    $ip_details = json_decode(file_get_contents("http://ipinfo.io/{$transaction->ip_address}/json"));
    
    if(!empty($transaction->local_tran_time)){
	    $trans_time = $transaction->local_tran_time;
	}else{
	    $trans_time = $transaction->created_at;
	}
?>

<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-7">
								<h4 class="text-left">Transaction Details</h4>
							</div>

							<div class="col-md-2">
								<?php if(isset($dispute)): ?>
									<?php if( $transaction->transaction_type_id == Payment_Sent && $transaction->status == 'Success' && $dispute->status != 'Open'): ?>
	                                    <a id="dispute_<?php echo e($transaction->id); ?>" href="<?php echo e(url('admin/dispute/add/'.$transaction->id)); ?>" class="btn button-secondary btn-sm pull-right">Open Dispute</a>
	                                <?php endif; ?>
								<?php endif; ?>
							</div>
							<div class="col-md-3">
								<?php if($transaction->status): ?>
									<h4 class="text-left">Status :
									<?php if($transaction->transaction_type_id == Deposit): ?>
		                        			<?php if($transaction->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Pending'): ?><span class="text-blue">Pending</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Blocked'): ?><span class="text-red">Cancelled</span><?php endif; ?>

		                        	<?php elseif($transaction->transaction_type_id == Withdrawal): ?>
											<?php if($transaction->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Pending'): ?><span class="text-blue">Pending</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Blocked'): ?><span class="text-red">Cancelled</span><?php endif; ?>

									<?php elseif($transaction->transaction_type_id == Transferred): ?>
											<?php if($transaction->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Pending'): ?><span class="text-blue">Pending</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Refund'): ?><span class="text-orange">Refunded</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Blocked'): ?><span class="text-red">Cancelled</span><?php endif; ?>

									<?php elseif($transaction->transaction_type_id == Received): ?>
											<?php if($transaction->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Pending'): ?><span class="text-blue">Pending</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Refund'): ?><span class="text-orange">Refunded</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Blocked'): ?><span class="text-red">Cancelled</span><?php endif; ?>


									<?php elseif($transaction->transaction_type_id == Exchange_From): ?>
											<?php if($transaction->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Blocked'): ?><span class="text-red">Cancelled</span><?php endif; ?>

									<?php elseif($transaction->transaction_type_id == Exchange_To): ?>
											<?php if($transaction->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Blocked'): ?><span class="text-red">Cancelled</span><?php endif; ?>

									<?php elseif($transaction->transaction_type_id == Request_From): ?>
											<?php if($transaction->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Pending'): ?><span class="text-blue">Pending</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Blocked'): ?><span class="text-red">Cancelled</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Refund'): ?><span class="text-orange">Refunded</span><?php endif; ?>

									<?php elseif($transaction->transaction_type_id == Request_To): ?>
											<?php if($transaction->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Pending'): ?><span class="text-blue">Pending</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Blocked'): ?><span class="text-red">Cancelled</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Refund'): ?><span class="text-orange">Refunded</span><?php endif; ?>

									<?php elseif($transaction->transaction_type_id == Payment_Sent): ?>
											<?php if($transaction->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Pending'): ?><span class="text-blue">Pending</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Refund'): ?><span class="text-orange">Refunded</span><?php endif; ?>

									<?php elseif($transaction->transaction_type_id == Payment_Received): ?>
											<?php if($transaction->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Pending'): ?><span class="text-blue">Pending</span><?php endif; ?>
		                        			<?php if($transaction->status == 'Refund'): ?><span class="text-orange">Refunded</span><?php endif; ?>

		                        	<?php elseif($transaction->transaction_type_id == Crypto_Sent): ?>
											<?php if($transaction->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
											<?php if($transaction->status == 'Pending'): ?><span class="text-blue">Pending</span><?php endif; ?>

									<?php elseif($transaction->transaction_type_id == Crypto_Received): ?>
											<?php if($transaction->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
											
									<?php elseif($transaction->transaction_type_id == Recharge): ?>
											<?php if($transaction->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
									<?php elseif($transaction->transaction_type_id == 32): ?>
											<?php if($transaction->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
									<?php endif; ?>
									</h4>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="panel-body">
						<div class="row">
							<form action="<?php echo e(url('admin/transactions/update/'.$transaction->id)); ?>" class="form-horizontal" id="transactions_form" method="POST">
								<?php echo e(csrf_field()); ?>

						        <input type="hidden" value="<?php echo e($transaction->id); ?>" name="id" id="id">
						        <input type="hidden" value="<?php echo e($transaction->transaction_type_id); ?>" name="transaction_type_id" id="transaction_type_id">
						        <input type="hidden" value="<?php echo e($transaction->transaction_reference_id); ?>" name="transaction_reference_id" id="transaction_reference_id">
						        <input type="hidden" value="<?php echo e($transaction->uuid); ?>" name="uuid" id="uuid">
						        <input type="hidden" value="<?php echo e($transaction->user_id); ?>" name="user_id" id="user_id">
						        <input type="hidden" value="<?php echo e($transaction->end_user_id); ?>" name="end_user_id" id="end_user_id">
						        <input type="hidden" value="<?php echo e($transaction->currency_id); ?>" name="currency_id" id="currency_id">
						        <input type="hidden" value="<?php echo e(($transaction->percentage)); ?>" name="percentage" id="percentage">
						        <input type="hidden" value="<?php echo e(($transaction->charge_percentage)); ?>" name="charge_percentage" id="charge_percentage">
						        <input type="hidden" value="<?php echo e(($transaction->charge_fixed)); ?>" name="charge_fixed" id="charge_fixed">
						        <input type="hidden" value="<?php echo e(base64_encode($transaction->payment_method_id)); ?>" name="payment_method_id" id="payment_method_id">

						        <input type="hidden" value="<?php echo e(base64_encode($transaction->merchant_id)); ?>" name="merchant_id" id="merchant_id">

						        <!--MerchantPayment-->
								<?php if(isset($transaction->merchant_payment)): ?>
									<input type="hidden" value="<?php echo e(base64_encode($transaction->merchant_payment->gateway_reference)); ?>" name="gateway_reference" id="gateway_reference">
							        <input type="hidden" value="<?php echo e($transaction->merchant_payment->order_no); ?>" name="order_no" id="order_no">
							        <input type="hidden" value="<?php echo e($transaction->merchant_payment->item_name); ?>" name="item_name" id="item_name">
								<?php endif; ?>

								<div class="col-md-6">
								    
								    <div class="panel panel-default">
										<div class="panel-body">

											<?php if($transaction->subtotal): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-6" for="subtotal">Amount</label>
													<input type="hidden" class="form-control" name="subtotal" value="<?php echo e($transaction->subtotal); ?>">
													<div class="col-sm-6">
													  <p class="form-control-static">
													  	<?php echo e($transaction->currency->type != 'fiat' ? moneyFormat($transaction->currency->symbol, $transaction->subtotal) :
													  	moneyFormat($transaction->currency->symbol, formatNumber($transaction->subtotal))); ?>

													  </p>
													</div>
												</div>
											<?php endif; ?>

						                    <div class="form-group total-deposit-feesTotal-space">
												<label class="control-label col-sm-6" for="fee">Fees
													<span>
														<small class="transactions-edit-fee">
															<?php if(isset($transaction)): ?>
																<?php if($transaction->currency->type != 'fiat'): ?>
																	(<?php echo e((($transaction->transaction_type->name == "Payment_Sent") ? "0" : ($transaction->percentage))); ?>% + <?php echo e(($transaction->charge_fixed)); ?>)
																<?php else: ?>
																	(<?php echo e((($transaction->transaction_type->name == "Payment_Sent") ? "0" : formatNumber($transaction->percentage))); ?>% + <?php echo e(formatNumber($transaction->charge_fixed)); ?>)
																<?php endif; ?>
															<?php else: ?>
																(<?php echo e(0); ?>%+<?php echo e(0); ?>)
															<?php endif; ?>
														</small>
													</span>
												</label>

												<?php
													$total_transaction_fees = $transaction->charge_percentage + $transaction->charge_fixed;
												?>

												<input type="hidden" class="form-control" name="fee" value="<?php echo e(($total_transaction_fees)); ?>">
												<div class="col-sm-6">
													<p class="form-control-static">
														<?php echo e($transaction->currency->type != 'fiat' ? moneyFormat($transaction->currency->symbol, $transaction->charge_fixed) :
														moneyFormat($transaction->currency->symbol, formatNumber($total_transaction_fees))); ?>

													</p>
												</div>
											</div>

											<hr class="increase-hr-height">

											<?php if($transaction->total): ?>
							                    <div class="form-group total-deposit-space">
													<label class="control-label col-sm-6" for="total">Total</label>
													<input type="hidden" class="form-control" name="total" value="<?php echo e(($transaction->total)); ?>">
													<div class="col-sm-6">
													  	<p class="form-control-static">
															<?php echo e($transaction->currency->type != 'fiat' ? moneyFormat($transaction->currency->symbol, str_replace("-",'',$transaction->total)) :
															moneyFormat($transaction->currency->symbol, str_replace("-",'',formatNumber($transaction->total)))); ?>

														</p>
													</div>
												</div>
											<?php endif; ?>

										</div>
									</div>
									
									<div class="panel panel-default">
										<div class="panel-body">

											
												<div class="form-group">
														<?php if($transaction->transaction_type_id == Deposit
															|| $transaction->transaction_type_id == Exchange_From
															|| $transaction->transaction_type_id == Exchange_To
															|| $transaction->transaction_type_id == Withdrawal
															|| $transaction->transaction_type_id == Payment_Sent
															|| $transaction->transaction_type_id == Payment_Received): ?>
														<label class="control-label col-sm-3" for="user">User</label>

														<?php elseif($transaction->transaction_type_id == Transferred): ?>
															<label class="control-label col-sm-3" for="user">Paid By</label>
														
														<?php elseif($transaction->transaction_type_id == Recharge): ?>
															<label class="control-label col-sm-3" for="user">Paid By</label>
                                                        <?php elseif($transaction->transaction_type_id == 32): ?>
                                                           <label class="control-label col-sm-3" for="user">Paid By</label>
														<?php elseif($transaction->transaction_type_id == Crypto_Sent || $transaction->transaction_type_id == Crypto_Received): ?>
															<label class="control-label col-sm-3" for="user">Sender</label>

														<?php elseif($transaction->transaction_type_id == Received): ?>
															<label class="control-label col-sm-3" for="user">Paid By</label>

														<?php elseif($transaction->transaction_type_id == Request_From): ?>
															<label class="control-label col-sm-3" for="user">Request From</label>

														<?php elseif($transaction->transaction_type_id == Request_To): ?>
															<label class="control-label col-sm-3" for="user">Request From</label>
														<?php endif; ?>

													<input type="hidden" class="form-control" name="user" value="
														<?php if(in_array($transaction->transaction_type_id, [Deposit, Transferred, Exchange_From, Exchange_To, Request_From, Withdrawal, Payment_Sent, Crypto_Sent,Recharge,32])): ?>
							                                <?php echo e(isset($transaction->user) ? $transaction->user->first_name.' '.$transaction->user->last_name :"-"); ?>

							                            <?php elseif(in_array($transaction->transaction_type_id, [Received, Request_To, Payment_Received, Crypto_Received])): ?>
							                                <?php echo e(isset($transaction->end_user) ? $transaction->end_user->first_name.' '.$transaction->end_user->last_name :"-"); ?>

							                            <?php endif; ?>
														">

													<div class="col-sm-9">
													  <p class="form-control-static">
														<?php if(in_array($transaction->transaction_type_id, [Deposit, Transferred, Exchange_From, Exchange_To, Request_From, Withdrawal, Payment_Sent, Crypto_Sent,Recharge,32])): ?>
							                                <?php echo e(isset($transaction->user) ? $transaction->user->first_name.' '.$transaction->user->last_name :"-"); ?>

							                            <?php elseif(in_array($transaction->transaction_type_id, [Received, Request_To, Payment_Received, Crypto_Received])): ?>
							                                <?php echo e(isset($transaction->end_user) ? $transaction->end_user->first_name.' '.$transaction->end_user->last_name :"-"); ?>

							                            <?php endif; ?>
														</p>
													</div>
												</div>

											
												<div class="form-group">
													<?php if($transaction->transaction_type_id == Deposit
															|| $transaction->transaction_type_id == Exchange_From
															|| $transaction->transaction_type_id == Exchange_To
															|| $transaction->transaction_type_id == Withdrawal
															|| $transaction->transaction_type_id == Payment_Sent
															|| $transaction->transaction_type_id == Payment_Received
															|| $transaction->transaction_type_id == Crypto_Sent
															|| $transaction->transaction_type_id == Crypto_Received): ?>
														<label class="control-label col-sm-3" for="receiver">Receiver</label>

													<?php elseif($transaction->transaction_type_id == Transferred): ?>
														<label class="control-label col-sm-3" for="receiver">Paid To</label>
														
                                                    <?php elseif($transaction->transaction_type_id == Recharge): ?>
														<label class="control-label col-sm-3" for="receiver">Recharge To</label>
													<?php elseif($transaction->transaction_type_id == 32): ?>
														<label class="control-label col-sm-3" for="receiver">Gift Card</label>	
													<?php elseif($transaction->transaction_type_id == Received): ?>
														<label class="control-label col-sm-3" for="user">Paid to</label>

													<?php elseif($transaction->transaction_type_id == Request_From): ?>
														<label class="control-label col-sm-3" for="receiver">Request To</label>

													<?php elseif($transaction->transaction_type_id == Request_To): ?>
														<label class="control-label col-sm-3" for="receiver">Request To</label>
													<?php endif; ?>

													<input type="hidden" class="form-control" name="receiver" value="
														 <?php switch($transaction->transaction_type_id):
							                                case (Deposit): ?>
							                                <?php case (Exchange_From): ?>
							                                <?php case (Exchange_To): ?>
							                                <?php case (Withdrawal): ?>
							                                <?php case (Crypto_Sent): ?>
							                                    <?php echo e(isset($transaction->end_user) ? $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name : "-"); ?>

							                                    <?php break; ?>
							                                <?php case (Transferred): ?>
							                                <?php case (Received): ?>

							                                        <?php if($transaction->transfer->receiver): ?>
							                                        <?php echo e($transaction->transfer->receiver->first_name.' '.$transaction->transfer->receiver->last_name); ?>

							                                        <?php elseif($transaction->transfer->email): ?>
							                                            <?php echo e($transaction->transfer->email); ?>

							                                        <?php elseif($transaction->transfer->phone): ?>
							                                            <?php echo e($transaction->transfer->phone); ?>

							                                        <?php else: ?>
							                                            <?php echo e('-'); ?>

							                                        <?php endif; ?>

							                                    <?php break; ?>
							                                <?php case (Request_From): ?>
							                                <?php case (Request_To): ?>
							                                    <?php echo e(isset($transaction->request_payment->receiver) ? $transaction->request_payment->receiver->first_name.' '.$transaction->request_payment->receiver->last_name : $transaction->request_payment->email); ?>

							                                    <?php break; ?>
							                                <?php case (Payment_Sent): ?>
							                                    <?php echo e(isset($transaction->end_user) ? $transaction->end_user->first_name.' '.$transaction->end_user->last_name :"-"); ?>

							                                    <?php break; ?>
							                                <?php case (Payment_Received): ?>
							                                <?php case (Crypto_Received): ?>
							                                    <?php echo e(isset($transaction->user) ? $transaction->user->first_name.' '.$transaction->user->last_name :"-"); ?>

							                                    <?php break; ?>
							                            <?php endswitch; ?>
															">

													<div class="col-sm-9">
													  	<p class="form-control-static">
														  	<?php switch($transaction->transaction_type_id):
								                                case (Deposit): ?>
								                                <?php case (Exchange_From): ?>
								                                <?php case (Exchange_To): ?>
								                                <?php case (Withdrawal): ?>
								                                <?php case (Crypto_Sent): ?>
								                               
								                                    <?php echo e(isset($transaction->end_user) ? $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name : "-"); ?>

								                                    <?php break; ?>
								                                <?php case (Transferred): ?>
								                                <?php case (Received): ?>

								                                        <?php if($transaction->transfer->receiver): ?>
								                                        <?php echo e($transaction->transfer->receiver->first_name.' '.$transaction->transfer->receiver->last_name); ?>

								                                        <?php elseif($transaction->transfer->email): ?>
								                                            <?php echo e($transaction->transfer->email); ?>

								                                        <?php elseif($transaction->transfer->phone): ?>
								                                            <?php echo e($transaction->transfer->phone); ?>

								                                        <?php else: ?>
								                                            <?php echo e('-'); ?>

								                                        <?php endif; ?>

								                                    <?php break; ?>
								                               <?php case (Recharge): ?>
								                                 <?php echo e($transaction->phone??'NA'); ?>

								                               <?php break; ?>
								                                <?php case (32): ?>
								                                   <?php
								                                        $giftdetails=DB::table('gift_cards')->where('id',$transaction->transaction_reference_id)->first();
								                                   ?>
								                                  <?php echo e($giftdetails->product_name); ?> || (Gift Card Number)<?php echo e($giftdetails->gift_card_number); ?> || (Gift Pin Code)<?php echo e($giftdetails->gift_pin_code); ?>

								                                 <?php echo e($transaction->phone??'NA'); ?>

								                               <?php break; ?>
								                                <?php case (Request_From): ?>
								                                <?php case (Request_To): ?>
								                                    <?php echo e(isset($transaction->request_payment->receiver) ? $transaction->request_payment->receiver->first_name.' '.$transaction->request_payment->receiver->last_name : $transaction->request_payment->email); ?>

								                                    <?php break; ?>
								                                <?php case (Payment_Sent): ?>
								                                    <?php echo e(isset($transaction->end_user) ? $transaction->end_user->first_name.' '.$transaction->end_user->last_name :"-"); ?>

								                                    <?php break; ?>
								                                <?php case (Payment_Received): ?>
								                                <?php case (Crypto_Received): ?>
								                                    <?php echo e(isset($transaction->user) ? $transaction->user->first_name.' '.$transaction->user->last_name :"-"); ?>

								                                    <?php break; ?>
								                            <?php endswitch; ?>
														</p>
													</div>
												</div>

											<!-- Sender Address -->
											<?php if(isset($senderAddress)): ?>
												<div class="form-group">
													<label class="control-label col-sm-3" for="crypto_sender_address">Sender Address</label>
													<input type="hidden" class="form-control" name="crypto_sender_address" value="<?php echo e($senderAddress); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static" id="crypto_sender_address"><?php echo e($senderAddress); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<!-- Receiver Address -->
											<?php if(isset($receiverAddress)): ?>
												<div class="form-group">
													<label class="control-label col-sm-3" for="crypto_receiver_address">Receiver Address</label>
													<input type="hidden" class="form-control" name="crypto_receiver_address" value="<?php echo e($receiverAddress); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static" id="crypto_receiver_address"><?php echo e($receiverAddress); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<!-- Txid -->
											<?php if(isset($txId)): ?>
												<div class="form-group">
													<label class="control-label col-sm-3" for="crypto_txid"><?php echo e($transaction->payment_method->name); ?> TxId</label>
													<input type="hidden" class="form-control" name="crypto_txid" value="<?php echo e($txId); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static" id="crypto_txid"><?php echo e(wordwrap($txId, 50, "\n", true)); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<!-- Confirmations -->
											<?php if(isset($confirmations)): ?>
												<div class="form-group">
													<label class="control-label col-sm-3" for="crypto_confirmations">Confirmations</label>
													<input type="hidden" class="form-control" name="crypto_confirmations" value="<?php echo e($confirmations); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static" id="crypto_confirmations"><?php echo e($confirmations); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($transaction->uuid): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-3" for="transactions_uuid">Transaction ID</label>
													<input type="hidden" class="form-control" name="transactions_uuid" value="<?php echo e($transaction->uuid); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($transaction->uuid); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($transaction->transaction_type_id): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-3" for="type">Type</label>
													<input type="hidden" class="form-control" name="type" value="<?php echo e(str_replace('_', ' ', $transaction->transaction_type->name)); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e(($transaction->transaction_type->name == "Withdrawal") ? "Payout" : str_replace('_', ' ', $transaction->transaction_type->name)); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($transaction->currency): ?>
												<div class="form-group">
													<label class="control-label col-sm-3" for="currency">Currency</label>
													<input type="hidden" class="form-control" name="currency" value="<?php echo e($transaction->currency->code); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($transaction->currency->code); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if(isset($transaction->payment_method_id)): ?>
												<div class="form-group">
													<label class="control-label col-sm-3" for="payment_method">Payment Method</label>
													<input type="hidden" class="form-control" name="payment_method" value="<?php echo e(($transaction->payment_method->name == "Mts") ? getCompanyName() : $transaction->payment_method->name); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static">
													      <?php 
													           if($transaction->payment_method->name == "Mts"){
													              echo getCompanyName();
													           }elseif($transaction->payment_method->name == "Stripe"){
													               echo "Debit/Credit Card";
													           }else{
													               echo $transaction->payment_method->name;
													           }
													       ?>
													  </p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($transaction->bank): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-3" for="bank_name">Bank Name</label>
													<input type="hidden" class="form-control" name="bank_name" value="<?php echo e($transaction->bank->bank_name); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($transaction->bank->bank_name); ?></p>
													</div>
												</div>

							                    <div class="form-group">
													<label class="control-label col-sm-3" for="bank_branch_name">Branch Name</label>
													<input type="hidden" class="form-control" name="bank_branch_name" value="<?php echo e($transaction->bank->bank_branch_name); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($transaction->bank->bank_branch_name); ?></p>
													</div>
												</div>

							                    <div class="form-group">
													<label class="control-label col-sm-3" for="account_name">Account Name</label>
													<input type="hidden" class="form-control" name="account_name" value="<?php echo e($transaction->bank->account_name); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($transaction->bank->account_name); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($transaction->file): ?>
												<div class="form-group">
													<label class="control-label col-sm-3" for="attached_file">Attached File</label>
													<div class="col-sm-9">
													  <p class="form-control-static">
										                  <a href="<?php echo e(url('public/uploads/files/bank_attached_files').'/'.$transaction->file->filename); ?>" download=<?php echo e($transaction->file->filename); ?>><i class="fa fa-fw fa-download"></i>
										                  	<?php echo e($transaction->file->originalname); ?>

										                  </a>
													  </p>
													</div>
												</div>
											<?php endif; ?>
											
											<div class="form-group">
												<label class="control-label col-sm-3" for="created_at">Date</label>
												<input type="hidden" class="form-control" name="created_at" value="<?php echo e($transaction->created_at); ?>">
												<div class="col-sm-9">
												  <p class="form-control-static"><?php echo e(Carbon\Carbon::parse($trans_time)->format('d-M-Y h:i A')); ?></p>
												</div>
											</div>

						               		<?php if($transaction->status): ?>
						               		  <?php if($transaction->transaction_type_id == 15): ?>
						               		  <?php else: ?>
						               		  <div class="form-group">
													<label class="control-label col-sm-3" for="status">Change Status</label>
													<div class="col-sm-9">

														<?php if(isset($transaction->refund_reference) && isset($transactionOfRefunded)): ?>
								                          	<p class="form-control-static"><span class="label label-success">Already Refunded</span></p>
								                          	<p class="form-control-static"><span class="label label-danger">Refund Reference: <i>
										                          	<a id="transactionOfRefunded" href="<?php echo e(url("admin/transactions/edit/$transactionOfRefunded->id")); ?>">( <?php echo e($transaction->refund_reference); ?> )</a>
										                          </i>
										                      </span>
										                  	</p>
										                <?php elseif($transaction->transaction_type_id == Crypto_Sent): ?>
								                          	<p class="form-control-static"><span class="label label-danger" id="crypto-sent-status" style="white-space: unset !important;">Crypto Sent Status Cannot Be Changed</span></p>
								                        <?php elseif($transaction->transaction_type_id == Crypto_Received): ?>
								                          	<p class="form-control-static"><span class="label label-danger" id="crypto-received-status" style="white-space: unset !important;">Crypto Received Status Cannot Be Changed</span></p>
									                    <?php else: ?>
															<select class="form-control select2" name="status" style="width: 60%;">

											                        <?php if($transaction->transaction_type_id == Deposit): ?>
																		<option value="Success" <?php echo e($transaction->status ==  'Success'? 'selected':""); ?>>Success</option>
												                        <option value="Pending"  <?php echo e($transaction->status == 'Pending' ? 'selected':""); ?>>Pending</option>
											                            <option value="Blocked"  <?php echo e($transaction->status == 'Blocked' ? 'selected':""); ?>>Cancel</option>

																	<?php elseif($transaction->transaction_type_id == Transferred || $transaction->transaction_type_id == Received): ?>
										                            	<?php if($transaction->status == 'Success'): ?>
																			<option value="Success" <?php echo e($transaction->status ==  'Success'? 'selected':""); ?>>Success</option>
											                            	<option value="Pending"  <?php echo e($transaction->status == 'Pending' ? 'selected':""); ?>>Pending</option>
											                            	<option value="Refund" <?php echo e($transaction->status ==  'Refund' ? 'selected':""); ?>>Refund</option>
											                            	<option value="Blocked"  <?php echo e($transaction->status == 'Blocked' ? 'selected':""); ?>>Cancel</option>
											                        	<?php else: ?>
											                        		<option value="Success" <?php echo e($transaction->status ==  'Success'? 'selected':""); ?>>Success</option>
											                            	<option value="Pending"  <?php echo e($transaction->status == 'Pending' ? 'selected':""); ?>>Pending</option>
											                            	<option value="Blocked"  <?php echo e($transaction->status == 'Blocked' ? 'selected':""); ?>>Cancel</option>
											                        	<?php endif; ?>

																	<?php elseif($transaction->transaction_type_id == Exchange_From || $transaction->transaction_type_id == Exchange_To): ?>
																		<option value="Success" <?php echo e($transaction->status ==  'Success'? 'selected':""); ?>>Success</option>
											                            <option value="Blocked"  <?php echo e($transaction->status == 'Blocked' ? 'selected':""); ?>>Cancel</option>

																	<?php elseif($transaction->transaction_type_id == Request_From || $transaction->transaction_type_id == Request_To): ?>
																	    <?php if($transaction->status == 'Pending'): ?>
												                        	<option value="Pending" <?php echo e($transaction->status ==  'Pending'? 'selected':""); ?>>Pending</option>
																			<option value="Blocked"  <?php echo e($transaction->status == 'Blocked' ? 'selected':""); ?>>Cancel</option>

																		<?php elseif($transaction->status == 'Blocked'): ?>
												                        	<option value="Pending" <?php echo e($transaction->status ==  'Pending'? 'selected':""); ?>>Pending</option>
																			<option value="Blocked"  <?php echo e($transaction->status == 'Blocked' ? 'selected':""); ?>>Cancel</option>

																		<?php elseif($transaction->status == 'Success'): ?>
												                        	<option value="Success" <?php echo e($transaction->status ==  'Success'? 'selected':""); ?>>Success</option>
																			<option value="Refund"  <?php echo e($transaction->status == 'Refund' ? 'selected':""); ?>>Refund</option>
																		<?php endif; ?>

																	<?php elseif($transaction->transaction_type_id == Withdrawal): ?>
																			<option value="Success" <?php echo e($transaction->status ==  'Success'? 'selected':""); ?>>Success</option>
													                        <option value="Pending"  <?php echo e($transaction->status == 'Pending' ? 'selected':""); ?>>Pending</option>
												                            <option value="Blocked"  <?php echo e($transaction->status == 'Blocked' ? 'selected':""); ?>>Cancel</option>

												                    <?php elseif($transaction->transaction_type_id == Payment_Sent || $transaction->transaction_type_id == Payment_Received): ?>
														                    <?php if($transaction->status ==  'Success'): ?>
												                        		<option value="Success" <?php echo e(isset($transaction->status) && $transaction->status ==  'Success'? 'selected':""); ?>>Success</option>
																				<option value="Pending"  <?php echo e(isset($transaction->status) && $transaction->status == 'Pending' ? 'selected':""); ?>>Pending</option>
																				<option value="Refund"  <?php echo e(isset($transaction->status) && $transaction->status == 'Refund' ? 'selected':""); ?>>Refund</option>
												                        	<?php else: ?>
												                        		<option value="Success" <?php echo e(isset($transaction->status) && $transaction->status ==  'Success'? 'selected':""); ?>>Success</option>
																				<option value="Pending"  <?php echo e(isset($transaction->status) && $transaction->status == 'Pending' ? 'selected':""); ?>>Pending</option>
												                        	<?php endif; ?>
																	<?php endif; ?>
															</select>
								                        <?php endif; ?>
													</div>
												</div>
						               		  <?php endif; ?>
						                   		
											<?php endif; ?>

										</div>
									</div>
								</div>

								<div class="col-md-6">
								    <?php if($transaction->transaction_type_id == Withdrawal): ?>
    								    <div class="panel panel-default">
    										<div class="panel-body">
											    <?php if(!empty($transaction->bank_id)): ?>
											        
											        <?php
											            $check_bank = DB::table('countries_bank')->where('id', $transaction->bank_id)->first();
											            $check_country = DB::table('countries')->where('id', $check_bank->country_id)->first();
											        ?>
											        
											        <div class="form-group">
														<label class="control-label col-sm-6" for="account_name">Country</label>
														<div class="col-sm-6">
														  <p class="form-control-static"><?php echo e($check_country->name); ?></p>
														</div>
													</div>
													
													<?php
                        
                                                        if(!empty($check_bank)){
                                                            $avail_fields = json_decode($check_bank->bank, true);
    	
                                                        	foreach($avail_fields as $k=>$avail_field){
                                                        	    
                                                        	?>
                                                        	    <div class="form-group">
            														<label class="control-label col-sm-6" for="account_name"><?php echo e($k); ?></label>
            														<input type="hidden" class="form-control" name="account_name" value="<?php echo e($avail_field); ?>">
            														<div class="col-sm-6">
            														  <p class="form-control-static"><?php echo e($avail_field); ?></p>
            														</div>
            													</div>
                                                        	<?php
                                                        	}
                                                        }
                                                    ?>
												<?php elseif(empty($transaction->bank_id) && $transaction->payment_method->name == 'Bank'): ?>
													<div class="form-group">
														<label class="control-label col-sm-6" for="account_name">Account Name</label>
														<input type="hidden" class="form-control" name="account_name" value="<?php echo e($transaction->withdrawal->withdrawal_detail->account_name); ?>">
														<div class="col-sm-6">
														  <p class="form-control-static"><?php echo e($transaction->withdrawal->withdrawal_detail->account_name); ?></p>
														</div>
													</div>

													<div class="form-group">
														<label class="control-label col-sm-6" for="account_number">Account Number/IBAN</label>
														<input type="hidden" class="form-control" name="account_number" value="<?php echo e($transaction->withdrawal->withdrawal_detail->account_number); ?>">
														<div class="col-sm-6">
														  <p class="form-control-static"><?php echo e($transaction->withdrawal->withdrawal_detail->account_number); ?></p>
														</div>
													</div>

													<div class="form-group">
														<label class="control-label col-sm-6" for="swift_code">SWIFT Code</label>
														<input type="hidden" class="form-control" name="swift_code" value="<?php echo e($transaction->withdrawal->withdrawal_detail->swift_code); ?>">
														<div class="col-sm-6">
														  <p class="form-control-static"><?php echo e($transaction->withdrawal->withdrawal_detail->swift_code); ?></p>
														</div>
													</div>

													<div class="form-group">
														<label class="control-label col-sm-6" for="bank_name">Bank Name</label>
														<input type="hidden" class="form-control" name="bank_name" value="<?php echo e($transaction->withdrawal->withdrawal_detail->bank_name); ?>">
														<div class="col-sm-6">
														  <p class="form-control-static"><?php echo e($transaction->withdrawal->withdrawal_detail->bank_name); ?></p>
														</div>
													</div>
												<?php endif; ?>
    									    </div>
    									</div>
    								<?php endif; ?>
									
								    <?php if(!empty($ip_details)): ?>
    									<div class="panel panel-default">
    										<div class="panel-body">
    										    <h3 class="text-center">Location</h3>
    										    
    										    <div class="form-group" style="margin-bottom: 0px;">
    												<label class="control-label col-sm-6" for="account_name">IP Address</label>
    												<div class="col-sm-6">
    												  <p class="form-control-static"><?php echo e($ip_details->ip); ?></p>
    												</div>
    											</div>
    											
    											<div class="form-group" style="margin-bottom: 0px;">
    												<label class="control-label col-sm-6" for="account_name">City</label>
    												<div class="col-sm-6">
    												  <p class="form-control-static"><?php echo e($ip_details->city); ?></p>
    												</div>
    											</div>
    											
    											<div class="form-group" style="margin-bottom: 0px;">
    												<label class="control-label col-sm-6" for="account_name">Region</label>
    												<div class="col-sm-6">
    												  <p class="form-control-static"><?php echo e($ip_details->region); ?></p>
    												</div>
    											</div>
    											
    											<div class="form-group" style="margin-bottom: 0px;">
    												<label class="control-label col-sm-6" for="account_name">Country</label>
    												<div class="col-sm-6">
    												  <p class="form-control-static"><?php echo e($ip_details->country); ?></p>
    												</div>
    											</div>
    											
    											<div class="form-group" style="margin-bottom: 0px;">
    												<label class="control-label col-sm-6" for="account_name">Postal Code</label>
    												<div class="col-sm-6">
    												  <p class="form-control-static"><?php echo e(!empty($ip_details->postal) ? $ip_details->postal : '-'); ?></p>
    												</div>
    											</div>
    											
    											<div class="form-group" style="margin-bottom: 0px;">
    												<label class="control-label col-sm-6" for="account_name">Timezone</label>
    												<div class="col-sm-6">
    												  <p class="form-control-static"><?php echo e($ip_details->timezone); ?></p>
    												</div>
    											</div>
    										</div>
    									</div>
    								<?php endif; ?>
									
									<?php if(!empty($transaction_details)): ?>
    									<div class="panel panel-default">
    										<div class="panel-body">
    										    <h3 class="text-center">Device</h3>
    										    
    										    <div class="form-group" style="margin-bottom: 0px;">
    												<label class="control-label col-sm-6" for="account_name">Device Name</label>
    												<div class="col-sm-6">
    												  <p class="form-control-static"><?php echo e($transaction_details->device_name); ?></p>
    												</div>
    											</div>
    											
    											<div class="form-group" style="margin-bottom: 0px;">
    												<label class="control-label col-sm-6" for="account_name">Brand</label>
    												<div class="col-sm-6">
    												  <p class="form-control-static"><?php echo e($transaction_details->device_manufacture); ?></p>
    												</div>
    											</div>
    											
    											<div class="form-group" style="margin-bottom: 0px;">
    												<label class="control-label col-sm-6" for="account_name">Model</label>
    												<div class="col-sm-6">
    												  <p class="form-control-static"><?php echo e($transaction_details->device_model); ?></p>
    												</div>
    											</div>
    											
    											<div class="form-group" style="margin-bottom: 0px;">
    												<label class="control-label col-sm-6" for="account_name">OS</label>
    												<div class="col-sm-6">
    												  <p class="form-control-static"><?php echo e($transaction_details->device_os); ?></p>
    												</div>
    											</div>
    											
    											<div class="form-group" style="margin-bottom: 0px;">
    												<label class="control-label col-sm-6" for="account_name">OS Version</label>
    												<div class="col-sm-6">
    												  <p class="form-control-static"><?php echo e($transaction_details->os_ver); ?></p>
    												</div>
    											</div>
    											
    											<div class="form-group" style="margin-bottom: 0px;">
    												<label class="control-label col-sm-6" for="account_name">App Version</label>
    												<div class="col-sm-6">
    												  <p class="form-control-static"><?php echo e($transaction_details->app_ver); ?></p>
    												</div>
    											</div>
    										</div>
    									</div>
    								<?php endif; ?>
								</div>

								<div class="row">
									<div class="col-md-11">
										<div class="col-md-2"></div>
										<div class="col-md-2"><a id="cancel_anchor" class="btn btn-danger pull-left" href="<?php echo e(url('admin/transactions')); ?>">Cancel</a></div>
										<?php if($transaction->transaction_type_id != Crypto_Sent && $transaction->transaction_type_id != Crypto_Received): ?>
											<?php if(!isset($transaction->refund_reference)): ?>
											  <?php if($transaction->transaction_type_id == 15): ?>
						               		  <?php else: ?>
												<div class="col-md-1">
													<button type="submit" class="btn button-secondary pull-right" id="request_payment">
														<i class="spinner fa fa-spinner fa-spin"></i> <span id="request_payment_text">Update</span>
													</button>
												</div>
											 <?php endif; ?>
											<?php endif; ?>
										<?php endif; ?>
									</div>
								</div>

							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<script type="text/javascript">

	$(window).on('load', function()
	{
		$(".select2").select2({});
	});

	// disabling submit and cancel button after clicking it
	$(document).ready(function ()
	{
	    $('form').submit(function ()
	    {
	        $("#transactions_edit").attr("disabled", true);
	        $('#cancel_anchor').attr("disabled", "disabled");
	        $(".fa-spin").show();
	        $("#transactions_edit_text").text('Updating...');

	        // Click False
	        $('#transactions_edit').click(false);
	        $('#cancel_anchor').click(false);
	    });

	    $('#transactionOfRefunded').css('color', 'white');
	});

</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/test/resources/views/admin/transactions/edit.blade.php ENDPATH**/ ?>