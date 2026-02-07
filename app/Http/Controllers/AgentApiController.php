<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Carbon\Carbon;

class AgentApiController extends Controller
{
    public function search()
    {
        $keyword = request()->query('keyword');
        $sender = request()->query('sender');
        $fromDate = request()->query('from_date');
        $toDate = request()->query('to_date');
        $unreadOnly = request()->query('unread_only', false);

        // Select only needed columns for better performance
        $query = Email::select('emails.id', 'emails.uuid', 'emails.subject', 'emails.sent_at',
                               'emails.html_body', 'emails.has_read', 'emails.sender_id',
                               'emails.sender_name');

        // Apply indexed filters first (date, read status)
        if ($unreadOnly === 'true' || $unreadOnly === 1 || $unreadOnly === true) {
            $query->where('emails.has_read', false);
        }

        if ($fromDate) {
            try {
                $fromDateTime = Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay();
                $query->where('emails.sent_at', '>=', $fromDateTime);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Invalid from_date format. Use YYYY-MM-DD'
                ], 400);
            }
        }

        if ($toDate) {
            try {
                $toDateTime = Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay();
                $query->where('emails.sent_at', '<=', $toDateTime);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Invalid to_date format. Use YYYY-MM-DD'
                ], 400);
            }
        }

        // Apply sender filter with join
        if ($sender) {
            $query->join('sender_email', 'emails.sender_id', '=', 'sender_email.id')
                  ->where('sender_email.email', 'like', '%' . $sender . '%');
        }

        // Apply FULLTEXT search (much faster than LIKE)
        if ($keyword) {
            $query->whereRaw("MATCH(emails.subject, emails.html_body) AGAINST(? IN BOOLEAN MODE)", [$keyword]);
        }

        $emails = $query->distinct()
            ->with('sender')
            ->orderBy('emails.sent_at', 'desc')
            ->limit(10)
            ->get();

        $response = [
            'count' => $emails->count(),
            'emails' => $emails->map(function ($email) {
                return [
                    'id' => $email->uuid,
                    'subject' => $email->subject,
                    'sender' => $email->sender ? $email->sender->email : $email->sender_name,
                    'date' => $email->sent_at->format('Y-m-d H:i:s'),
                    'preview' => $email->getPreview(),
                    'unread' => !$email->has_read,
                ];
            })->values(),
        ];

        return response()->json($response);
    }

    public function show($id)
    {
        $email = Email::where('uuid', $id)->first();

        if (!$email) {
            return response()->json([
                'error' => "Email with ID '{$id}' not found"
            ], 404);
        }

        $email->load('sender');

        return response()->json([
            'id' => $email->uuid,
            'subject' => $email->subject,
            'sender' => $email->sender ? $email->sender->email : $email->sender_name,
            'date' => $email->sent_at->format('Y-m-d H:i:s'),
            'body' => strip_tags($email->html_body),
        ]);
    }
}
