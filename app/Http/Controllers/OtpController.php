<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = [
            'name' => $request->query('name', ''),
            'email' => $request->query('email', ''),
            'number' => $request->query('number', ''),
            'from_date' => $request->query('from_date', ''),
            'to_date' => $request->query('to_date', ''),
        ];
        
        $query = Otp::query();

        if ($filters['name']) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }
        if ($filters['email']) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }
        if ($filters['number']) {
            $query->where('number', 'like', '%' . $filters['number'] . '%');
        }
        if ($filters['from_date']) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }
        if ($filters['to_date']) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        $otps = $query->orderByDesc('created_at')->paginate(20);
        return view('otps.index', compact('otps', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'number' => 'required|string|max:20',
            ]);

            $otpCode = random_int(100000, 999999);
            $data['otp'] = (string)$otpCode;
            do {
                $tracking_id = strtoupper(substr(bin2hex(random_bytes(8)), 0, 8));
            } while (Otp::where('tracking_id', $tracking_id)->exists());
            $data['tracking_id'] = $tracking_id;

            $appUrl = config('app.url', url('/'));
            $trackLink = $appUrl . '/?tracking_id=' . $tracking_id;
            $msg = "Your OTP is: $otpCode\nTracking ID: $tracking_id\nTrack your complaint: $trackLink";

            if (!$this->sendOtpSms($data['number'], $msg)) {
                return response()->json(['success' => false, 'message' => 'Failed to send OTP SMS.'], 500);
            }

            $otp = Otp::create($data);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent and stored successfully.',
                'otp_id' => $otp->id
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    protected function sendOtpSms($number, $msg)
    {
        $number = $this->formatNumber($number);
        $transactionId = $this->generateTransactionId($number);

        $smsConfig = config('complaints.sms');
        $apiUrl = $smsConfig['api_url'] ?? 'https://api.itelservices.net/send.php';
        $apiKey = $smsConfig['api_key'] ?? '';
        $from = $smsConfig['from'] ?? '';
        $type = $smsConfig['type'] ?? 'sms';

        $params = [
            'transaction_id' => $transactionId,
            'api_key' => $apiKey,
            'number' => $number,
            'text' => $msg,
            'from' => $from,
            'type' => $type,
        ];
        $url = $apiUrl . '?' . http_build_query($params);

        $contextOptions = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ]
        ];

        $context = stream_context_create($contextOptions);
        $smsResponse = @file_get_contents($url, false, $context);
        if ($smsResponse === false) {
            return false;
        }
        return strpos($smsResponse, 'Status: 013') !== false;
    }

    public function verifyOtp(Request $request)
    {
        try {
            $data = $request->validate([
                'otp_id' => 'required|integer|exists:otps,id',
                'otp' => 'required|string',
            ]);

            $otp = Otp::find($data['otp_id']);
            if ($otp && $otp->otp === $data['otp']) {
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function formatNumber($number)
    {
        $number = preg_replace('/\D/', '', $number);
        if (substr($number, 0, 1) === '0') {
            $number = substr($number, 1);
        }
        if (substr($number, 0, 2) !== '92') {
            $number = '92' . $number;
        }
        return $number;
    }

    private function generateTransactionId($number)
    {
        $timestamp = date('YmdHis');
        $lastDigits = substr($number, -4);
        return $timestamp . '_' . $lastDigits;
    }
    /**
     * Display the specified resource.
     */
    public function show(Otp $otp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Otp $otp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Otp $otp)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Otp $otp)
    {
        //
    }
}
