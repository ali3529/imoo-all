<?php

namespace App\Http\Controllers\Properties;

use App\Enums\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Payrexx\Models\Request\Gateway;
use Payrexx\Payrexx;
use Payrexx\PayrexxException;

class PayrexxCallbackController extends Controller
{
    public function failed(Request $request)
    {
        $transaction = $this->getTransaction($request);

        if (!$transaction) {
            return '::error';
        }
        $paymentId = $request->get('c', 0);


        if ($transaction->getStatus() !== 'confirmed') {
            $payment = Payment::find($paymentId);
            $payment->status = PaymentStatusEnum::FAILED();
//            $payment->payed = 1;
            $payment->save();
            $property = $payment->property()->first();

            return redirect()->to('/properties/'.$property->reference
                .'/details/failed');
        }
        return 'failed';
    }

    public function success(Request $request)
    {
//        $transaction = !$request->get('transaction', []);

        $transaction = $this->getTransaction($request);

        if (!$transaction) {
            return '::error';
        }
        $paymentId = $request->get('c', 0);


        if ($transaction->getStatus() !== 'confirmed') {
            $payment = Payment::find($paymentId);
            $payment->status = PaymentStatusEnum::FAILED();
//            $payment->payed = 1;
            $payment->save();
            $property = $payment->property()->first();

            return redirect()->to('/properties/'.$property->reference
                .'/details/confirmation');
        }

        $payment = Payment::find($paymentId);

//        $invoice = $transaction['invoice'];
        $payment->status = PaymentStatusEnum::COMPLETED;
//        $payment->charge_id = $invoice['paymentRequestId'];
//         $payment->payed = 1;
        $payment->save();
        $property = $payment->property()->first();

        return redirect()->to('/properties/'.$property->reference
            .'/details/confirmation');
    }

    public function cancel(Request $request)
    {
//        $transaction = !$request->get('transaction', []);
        $transaction = $this->getTransaction($request);

        if (!$transaction) {
            return '::error';
        }
        $paymentId = $request->get('c', 0);
        if ($transaction->getStatus() !== 'confirmed') {
            $payment = Payment::find($paymentId);
            $payment->status = PaymentStatusEnum::FAILED();
            $payment->save();
            $property = $payment->property()->first();
            $property->payed = 1;
            $property->save();

            return redirect()->to('/properties/'.$property->reference
                .'/details/failed');
        }
        return 'cancel';
    }

    private function getTransaction($request)
    {
        $payrexx = new Payrexx(config('services.payrexx.instance'),
            config('services.payrexx.key'));

        $payment = Payment::where('id', '=', $request->get('c', 0))
            ->where('status', PaymentStatusEnum::PENDING())->first();
        if ($payment) {

            $id = $payment->order_id;
            $gateway = new Gateway();
            $gateway->setId($id);

            try {
                return $response = $payrexx->getOne($gateway);
            } catch (PayrexxException $e) {
                print $e->getMessage();
            }
        }
        return null;
    }
}
