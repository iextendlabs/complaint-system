<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AdminComplaintNotification;
use App\Mail\UserComplaintNotification;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255',
                'date' => 'nullable|date',
                'status' => 'nullable|string|in:' . implode(',', array_keys(config('complaints.statuses'))),
                'confidential' => 'nullable|boolean',
                'accepted' => 'nullable|boolean',
            ]);

            return redirect()->route('home', array_filter($validated));
        }

        $filters = [
            'name' => $request->query('name', ''),
            'email' => $request->query('email', ''),
            'tracking_id' => $request->query('tracking_id', ''),
            'from_date' => $request->query('from_date', ''),
            'to_date' => $request->query('to_date', ''),
            'status' => $request->query('status', ''),
            'confidential' => $request->query('confidential', false),
            'accepted' => $request->query('accepted', false),
        ];

        $query = Complaint::query();

        if ($name = $request->query('name')) {
            $query->where('name', 'like', "%{$name}%");
        }
        if ($email = $request->query('email')) {
            $query->where('email', 'like', "%{$email}%");
        }
        if ($tracking_id = $request->query('tracking_id')) {
            $query->where('tracking_id', $tracking_id);
        }
        if ($from_date = $request->query('from_date')) {
            $query->whereDate('created_at', '>=', $from_date);
        }
        if ($to_date = $request->query('to_date')) {
            $query->whereDate('created_at', '<=', $to_date);
        }
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($request->query('confidential')) {
            $query->where('isConfidential', true);
        }
        if ($request->query('accepted')) {
            $query->where('declarationAccepted', true);
        }

        $complaints = $query->paginate(10);
        return view('home', compact('complaints', 'filters'));
    }

    /**
     * Store a new complaint.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'number' => 'required|integer',
                'email' => 'required|email|max:255',
                'complaint' => 'required|string',
                'tracking_id' => 'required|integer|unique:complaints',
                'isConfidential' => 'boolean',
                'declarationAccepted' => 'required|boolean',
                'file' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:2048',
            ]);

            $data = $request->only(['name', 'number', 'email', 'complaint', 'isConfidential', 'declarationAccepted', 'tracking_id']);
            $data['status'] = 'Open'; 

            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('uploads', 'public');
                $data['file'] = $filePath;
            }

            $complaint = Complaint::create($data);

            try {
                // Send email to admin
                Mail::to(env('MAIL_TO_ADDRESS', config('mail.from.address')))->send(new AdminComplaintNotification($complaint));
                // Send email to user
                Mail::to($complaint->email)->send(new UserComplaintNotification($complaint));
            } catch (\Exception $e) {
                Log::error('Failed to send complaint emails: ' . $e->getMessage());
            }

            return response()->json(['success' => true, 'message' => 'Complaint submitted successfully.', 'tracking_id' => $complaint->tracking_id], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }

    /**
     * Export complaints to CSV.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export(Request $request)
    {
        $query = Complaint::query();

        if ($name = $request->query('name')) {
            $query->where('name', 'like', "%{$name}%");
        }
        if ($email = $request->query('email')) {
            $query->where('email', 'like', "%{$email}%");
        }
        if ($tracking_id = $request->query('tracking_id')) {
            $query->where('tracking_id', $tracking_id);
        }
        if ($from_date = $request->query('from_date')) {
            $query->whereDate('created_at', '>=', $from_date);
        }
        if ($to_date = $request->query('to_date')) {
            $query->whereDate('created_at', '<=', $to_date);
        }
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($request->query('confidential')) {
            $query->where('isConfidential', true);
        }
        if ($request->query('accepted')) {
            $query->where('declarationAccepted', true);
        }

        $complaints = $query->get();

        $filename = 'complaints_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($complaints) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Tracking ID',
                'Name',
                'Number',
                'Email',
                'Complaint',
                'Status',
                'Confidential',
                'Declaration Accepted',
                'File',
                'Created At',
                'Updated At'
            ]);

            foreach ($complaints as $complaint) {
                fputcsv($handle, [
                    $complaint->id,
                    $complaint->tracking_id,
                    $complaint->name,
                    $complaint->number,
                    $complaint->email,
                    $complaint->complaint,
                    $complaint->status,
                    $complaint->isConfidential ? 'Yes' : 'No',
                    $complaint->declarationAccepted ? 'Yes' : 'No',
                    $complaint->file ? asset('storage/' . $complaint->file) : '',
                    $complaint->created_at->format('Y-m-d H:i:s'),
                    $complaint->updated_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Update the status of a complaint.
     *
     * @param Request $request
     * @param Complaint $complaint
     * @return JsonResponse
     */
    public function updateStatus(Request $request, Complaint $complaint): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:' . implode(',', array_keys(config('complaints.statuses'))),
        ]);

        $complaint->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
}
