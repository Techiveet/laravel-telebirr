<?php

namespace Techive\Telebirr\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Techive\Telebirr\Facades\Telebirr;
use Illuminate\Support\Facades\Log;

class TelebirrController extends Controller
{
    public function notify(Request $request)
    {
        Log::info('Telebirr Notification Received:', $request->all());

        $decryptedData = json_decode(urldecode($request->getContent()), true);
        
        if (Telebirr::verifyNotification($decryptedData)) {
            Log::info('Telebirr Notification Verified Successfully.');
            // TODO: Add your logic here
            // e.g., Get the order ID from $decryptedData['outTradeNo']
            // Update the order status in your database to 'paid'
            // $order = Order::where('trade_no', $decryptedData['outTradeNo'])->first();
            // $order->update(['status' => 'paid']);
            
            return response()->json(['code' => 0, 'msg' => 'success']);
        }

        Log::error('Telebirr Notification Verification Failed.');
        return response()->json(['code' => 1, 'msg' => 'fail'], 400);
    }

    public function return(Request $request)
    {
        Log::info('User returned from Telebirr:', $request->all());
        
        // TODO: Add your logic here
        // Get the order ID from $request->get('outTradeNo')
        // Check the order status in your database
        // Redirect the user to a success or failure page
        // return redirect()->route('order.status', ['trade_no' => $request->get('outTradeNo')]);
        
        // For now, just show a simple message
        return 'Payment process completed. Please check your order status.';
    }
}