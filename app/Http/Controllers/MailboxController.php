<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Email;
use Illuminate\Support\Str;
use App\MailboxConfig;

class MailboxController extends Controller
{
    protected $DEFAULT_FOLDER = 'inbox';

    public function metadata()
    {
        $groups = MailboxConfig::GROUPS;

        return response()->json(array_values($groups));
    }

    public function show($uuid)
    {
        $email = Email::where('uuid', $uuid)->first();

        $email->has_read = true;
        $email->save();

        $email->load('sender');
        $email->created_at_human = $email->created_at->diffForHumans();

        return response()->json($email);
    }

    public function index($group)
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

        // Apply rules with OR logic between different rule types
        if (!empty($rules)) {
            $emailQuery->where(function ($query) use ($rules) {
                foreach ($rules as $ruleType => $selectors) {
                    if ($ruleType === 'exclude_from') {
                        // exclude_from should still apply as AND (it's an exclusion)
                        $this->filterExcludeFromField($query, $selectors);
                    } else {
                        // Other rules should be OR'd together
                        $query->orWhere(function ($subQuery) use ($ruleType, $selectors) {
                            $this->filterBasedOnRule($subQuery, $ruleType, $selectors);
                        });
                    }
                }
            });
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
        // Handle exclusion rules
        if ($ruleType === 'exclude_from') {
            $this->filterExcludeFromField($query, $selectors);
            return;
        }

        $functionName = 'filter' . Str::studly($ruleType) . 'Field';
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
     * Apply 'sender_name' rule - filter by sender name
     */
    private function filterSenderNameField($query, $selectors)
    {
        $query->where(function ($q) use ($selectors) {
            foreach ($selectors as $selector) {
                $q->orWhere('sender_name', $selector);
            }
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
     * Apply 'exclude_from' rule - exclude emails matching other groups
     */
    private function filterExcludeFromField($query, $groupPaths)
    {
        $allGroups = MailboxConfig::GROUPS;

        // Collect all rules from groups to exclude
        $excludeRules = [];
        foreach ($allGroups as $group) {
            if (in_array($group['path'], $groupPaths)) {
                $excludeRules[] = $group['rules'];
            }
        }

        // Apply exclusions using whereDoesntHave for 'from' rules
        foreach ($excludeRules as $rules) {
            if (isset($rules['from'])) {
                $query->whereDoesntHave('sender', function ($q) use ($rules) {
                    $q->where(function ($subQ) use ($rules) {
                        foreach ($rules['from'] as $selector) {
                            $this->applyEmailPattern($subQ, 'email', $selector);
                        }
                    });
                });
            }

            // Apply exclusions for 'to' rules
            if (isset($rules['to'])) {
                foreach ($rules['to'] as $selector) {
                    $this->applyNegativeEmailPattern($query, 'to', $selector);
                }
            }

            // Apply exclusions for 'sender_name' rules
            if (isset($rules['sender_name'])) {
                foreach ($rules['sender_name'] as $selector) {
                    $query->where('sender_name', '!=', $selector);
                }
            }
        }
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

    /**
     * Apply negative email pattern matching (NOT LIKE)
     */
    private function applyNegativeEmailPattern($query, $field, $pattern)
    {
        if (str_starts_with($pattern, '*@')) {
            $domain = substr($pattern, 2);
            $query->where($field, 'not like', '%@' . $domain);
        } else {
            $query->where($field, '!=', $pattern);
        }
    }
}
