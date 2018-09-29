<?php
/**
 * Abstract class for Payment Gateways
 */
namespace App\Services\Payments;


abstract class PaymentModule
{
	public abstract function getPaymentName();
	
	public abstract function getPaymentType();

	public abstract function ProcessPayment($cart_id);
	
	public abstract function extractEmail($data);

	public abstract function extractName($data);

	public abstract function extractAddress($data);
	
	public abstract function extractStatus($data);

	public abstract function extractItems($data);

}
