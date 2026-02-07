<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Carbon\Carbon;

class EmailApiController extends Controller
{
    public function search()
    {
        $keyword = request()->query('keyword');
        $sender = request()->query('sender');
        $fromDate = request()->query('from_date');
        $toDate = request()->query('to_date');
        $unreadOnly = request()->query('unread_only', false);

        $query = Email::query();

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('subject', 'like', '%' . $keyword . '%')
                  ->orWhere('html_body', 'like', '%' . $keyword . '%');
            });
        }

        if ($sender) {
            $query->whereHas('sender', function ($q) use ($sender) {
                $q->where('email', 'like', '%' . $sender . '%');
            });
        }

        if ($fromDate) {
            try {
                $fromDateTime = Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay();
                $query->where('sent_at', '>=', $fromDateTime);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Invalid from_date format. Use YYYY-MM-DD'
                ], 400);
            }
        }

        if ($toDate) {
            try {
                $toDateTime = Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay();
                $query->where('sent_at', '<=', $toDateTime);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Invalid to_date format. Use YYYY-MM-DD'
                ], 400);
            }
        }

        if ($unreadOnly === 'true' || $unreadOnly === 1 || $unreadOnly === true) {
            $query->where('has_read', false);
        }

        $emails = $query->orderBy('sent_at', 'desc')
            ->limit(10)
            ->get();

        $response = [
            'count' => $emails->count(),
            'emails' => $emails->map(function ($email) {
                return [
                    'id' => $email->uuid,
                    'subject' => $email->subject,
                    'sender' => $email->sender ? $email->sender->email : $email->sender_name,
                    'date' => $this->formatDate($email->sent_at),
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
            'date' => $this->formatDate($email->sent_at),
            'body' => strip_tags($email->html_body),
        ]);
    }

    private function formatDate($date)
    {
        if ($date instanceof \Carbon\Carbon) {
            return $date->format('Y-m-d');
        }

        if (is_string($date)) {
            try {
                return Carbon::parse($date)->format('Y-m-d');
            } catch (\Exception $e) {
                return $date;
            }
        }

        return null;
    }
}
