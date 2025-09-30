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
        $data = $request->validate([
            'tracking_id' => 'required|integer|exists:complaints,tracking_id',
        ]);

        $complaint = Complaint::where('tracking_id', $data['tracking_id'])->first();

        if (!$complaint) {
            return view('tracking', ['error' => 'Complaint not found.']);
        }

        return view('tracking', ['complaint' => $complaint]);
    }
}
