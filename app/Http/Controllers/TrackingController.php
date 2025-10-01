<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;

class TrackingController extends Controller
{
    /**
     * Track a complaint by tracking ID and email.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function track(Request $request)
    {
        $complaint = null;
        $error = null;
        if ($request->filled('tracking_id')) {
            $request->validate([
                'tracking_id' => 'required',
            ]);
            $complaint = Complaint::where('tracking_id', $request->input('tracking_id'))->first();
            if (!$complaint) {
                $error = 'Complaint not found.';
            }
        }
        return view('tracking', [
            'complaint' => $complaint,
            'error' => $error,
        ]);
    }
}
