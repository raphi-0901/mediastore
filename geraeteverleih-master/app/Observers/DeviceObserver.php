<?php

namespace App\Observers;

use App\Device;
use App\Order;
use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DeviceObserver
{
    /**
     * Handle the device "created" event.
     *
     * @param \App\Device $device
     * @return void
     */
    public function created(Device $device)
    {
        /* try {
             $path = public_path("/qrCodes/");
             if (!File::isDirectory($path))
                 File::makeDirectory($path, 0777, true, true);

             QrCode::size(250)
                 ->margin(2)
                 ->encoding('UTF-8')
                 ->errorCorrection('H')
                 ->generate($device->qr_id, $path . 'qr_' . $device->qr_id . '.svg');
         } catch (\Exception $ex) {
         }*/
    }

    /**
     * Handle the device "updated" event.
     *
     * @param \App\Device $device
     * @return void
     */
    public function updated(Device $device)
    {
        //
    }

    /**
     * Handle the device "deleted" event.
     *
     * @param \App\Device $device
     * @return void
     */
    public function deleted(Device $device)
    {
        foreach ($device->orders as $order) {
            if ($order->status()[0] < 3)
            {
                $order->devices()->detach($device->id);

                $order = Order::find($order->id);
                //delete when 0 devices left
                if ($order->devices->count() == 0)
                    $order->delete();
            }
        }
    }

    /**
     * Handle the device "restored" event.
     *
     * @param \App\Device $device
     * @return void
     */
    public function restored(Device $device)
    {
        //
    }

    /**
     * Handle the device "force deleted" event.
     *
     * @param \App\Device $device
     * @return void
     */
    public function forceDeleted(Device $device)
    {
        //
    }
}
