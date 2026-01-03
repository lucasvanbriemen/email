<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\MailboxConfig;

class MailboxController extends Controller
{
    protected $DEFAULT_FOLDER = 'inbox';

    public function metadata()
    {
        $groups = MailboxConfig::GROUPS;

        return response()->json(array_values($groups));
    }

    public function emails($group)
    {
        $allGroups = MailboxConfig::GROUPS;
        $groupConfig = null;

        foreach ($allGroups as $allGroupsItem) {
            if ($allGroupsItem['path'] === $group) {
                $groupConfig = $allGroupsItem;
                break;
            }
        }

        $emailQuery = Email::query();

        $rules = $groupConfig['rules'];
        foreach ($rules as $ruleType => $selectors) {
            $this->filterBasedOnRule($emailQuery, $ruleType, $selectors);
        }

        // Apply search filter if search query provided
        $search = request()->query('search', null);
        if ($search) {
            $emailQuery->where(function ($q) use ($search) {
                $q->where('subject', 'like', '%' . $search . '%')
                  ->orWhere('to', 'like', '%' . $search . '%')
                  ->orWhere('sender_name', 'like', '%' . $search . '%');
            });
        }

        $page = request()->query('page', 1);
        $emails = $emailQuery->orderBy('sent_at', 'desc')->paginate(50, ['id', 'uuid', 'subject', 'created_at', 'to', 'sender_name', 'sender_id'], 'page', $page);

        foreach ($emails as $email) {
            $email->created_at_human = $email->created_at->diffForHumans();
            $email->load('sender');
        }

        return response()->json($emails);
    }

    /**
     * Apply a rule filter to the query based on rule type
     */
    private function filterBasedOnRule($query, $ruleType, $selectors)
    {
        $functionName = 'filter' . ucfirst($ruleType) . 'Field';
        if (method_exists($this, $functionName)) {
            $this->$functionName($query, $selectors);
            return;
        }
    }

    /**
     * Apply 'from' rule - filter by sender email
     */
    private function filterFromField($query, $selectors)
    {
        $query->whereHas('sender', function ($q) use ($selectors) {
            $q->where(function ($subQ) use ($selectors) {
                foreach ($selectors as $selector) {
                    $this->applyEmailPattern($subQ, 'email', $selector);
                }
            });
        });
    }

    /**
     * Apply 'to' rule - filter by recipient email
     */
    private function filterToField($query, $selectors)
    {
        $query->where(function ($q) use ($selectors) {
            foreach ($selectors as $selector) {
                $this->applyEmailPattern($q, 'to', $selector);
            }
        });
    }

    /**
     * Apply email pattern matching (handles, *@domain, exact)
     */
    private function applyEmailPattern($query, $field, $pattern)
    {
        if (str_starts_with($pattern, '*@')) {
            $domain = substr($pattern, 2);
            $query->orWhere($field, 'like', '%@' . $domain);
        } else {
            $query->orWhere($field, $pattern);
        }
    }

    public function email($uuid)
    {
        $email = Email::where('uuid', $uuid)->first();

        $email->has_read = true;
        $email->save();

        $email->load('sender');
        $email->created_at_human = $email->created_at->diffForHumans();

        return response()->json($email);
    }
}
