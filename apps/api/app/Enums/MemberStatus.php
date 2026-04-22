<?php

namespace App\Enums;

/**
 * Voting eligibility status returned by the user-info service (Bonus 1).
 */
enum MemberStatus: string
{
    case Eligible = 'ABLE_TO_VOTE';
    case Ineligible = 'UNABLE_TO_VOTE';
}
