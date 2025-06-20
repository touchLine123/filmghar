<?php
namespace App\Traits;

use App\Constants\Status;

trait UserNotify {
    public static function notifyToUser() {
        return [
            'allUsers'               => 'All Users',
            'selectedUsers'          => 'Selected Users',
            'withBalance'            => 'With Balance Users',
            'emptyBalanceUsers'      => 'Empty Balance Users',
            'hasDepositedUsers'      => 'Paymented Users',
            'notDepositedUsers'      => 'Not Paymented Users',
            'pendingDepositedUsers'  => 'Pending Paymented Users',
            'rejectedDepositedUsers' => 'Rejected Paymented Users',
            'topDepositedUsers'      => 'Top Paymented Users',
            'pendingTicketUser'      => 'Pending Ticket Users',
            'answerTicketUser'       => 'Answer Ticket Users',
            'closedTicketUser'       => 'Closed Ticket Users',
            'notLoginUsers'          => 'Last Few Days Not Login Users',
        ];
    }

    public function scopeSelectedUsers($query) {
        return $query->whereIn('id', request()->user ?? []);
    }

    public function scopeAllUsers($query) {
        return $query;
    }

    public function scopeEmptyBalanceUsers($query) {
        return $query->where('balance', '<=', 0);
    }

    public function scopeTwoFaDisableUsers($query) {
        return $query->where('ts', Status::DISABLE);
    }

    public function scopeTwoFaEnableUsers($query) {
        return $query->where('ts', Status::ENABLE);
    }

    public function scopeHasDepositedUsers($query) {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        });
    }

    public function scopeNotDepositedUsers($query) {
        return $query->whereDoesntHave('deposits', function ($q) {
            $q->successful();
        });
    }

    public function scopePendingDepositedUsers($query) {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->pending();
        });
    }

    public function scopeRejectedDepositedUsers($query) {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->rejected();
        });
    }

    public function scopeTopDepositedUsers($query) {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        })->withSum(['deposits' => function ($q) {
            $q->successful();
        }], 'amount')->orderBy('deposits_sum_amount', 'desc')->take(request()->number_of_top_deposited_user ?? 10);
    }

    public function scopePendingTicketUser($query) {
        return $query->whereHas('tickets', function ($q) {
            $q->whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY]);
        });
    }

    public function scopeClosedTicketUser($query) {
        return $query->whereHas('tickets', function ($q) {
            $q->where('status', Status::TICKET_CLOSE);
        });
    }

    public function scopeAnswerTicketUser($query) {
        return $query->whereHas('tickets', function ($q) {

            $q->where('status', Status::TICKET_ANSWER);
        });
    }

    public function scopeNotLoginUsers($query) {
        return $query->whereDoesntHave('loginLogs', function ($q) {
            $q->whereDate('created_at', '>=', now()->subDays(request()->number_of_days ?? 10));
        });
    }
}
