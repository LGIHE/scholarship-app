<?php

namespace App\Support;

/**
 * Single source of truth for the scholarship eligibility criteria.
 *
 * An application is eligible for reports and analytics only when it satisfies
 * ALL THREE conditions:
 *   1. At least one of the applicant's teaching subjects matches an approved subject.
 *   2. The applicant's academic programme matches the approved course.
 *   3. The applicant's gender is Female (NIN prefix "CF").
 *
 * The matching is intentionally fuzzy (case-insensitive, partial-string) so that
 * common typos and abbreviations still resolve correctly.
 */
class ApprovedCriteria
{
    // ─────────────────────────────────────────────────────────────────────────
    // Approved subjects
    // Each entry is a lowercase keyword; if the stored subject *contains* any
    // of these keywords it is considered a match.
    // ─────────────────────────────────────────────────────────────────────────
    private const SUBJECT_KEYWORDS = [
        'biology',
        'chemistry',
        'physics',
        'mathematics',
        'maths',
        'math',
        'agriculture',
        'computer studies',
        'computer science',
        'ict',
        'information and communication',
        'information technology',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Approved course
    // Each entry is a lowercase keyword; if the stored programme *contains* any
    // of these keywords it is considered a match.
    // ─────────────────────────────────────────────────────────────────────────
    private const COURSE_KEYWORDS = [
        'bachelor of science with education',
        'b.sc. with education',
        'bsc with education',
        'b.sc with education',
        'bsc. with education',
        'bachelor of science in education',
        'b.sc. in education',
        'bsc in education',
        'science with education',
        'science education',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Approved gender — NIN prefixes that map to Female
    // ─────────────────────────────────────────────────────────────────────────
    private const FEMALE_NIN_PREFIX = 'CF';

    // ─────────────────────────────────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns true if the raw subject string matches any approved subject.
     */
    public static function subjectMatches(string $raw): bool
    {
        $normalised = strtolower(trim($raw));
        if ($normalised === '') {
            return false;
        }

        foreach (self::SUBJECT_KEYWORDS as $keyword) {
            if (str_contains($normalised, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if either teaching subject (1 or 2) in personal_info
     * matches an approved subject.
     */
    public static function hasApprovedSubject(array $personalInfo): bool
    {
        $subject1 = (string) ($personalInfo['teaching_subjects_1'] ?? '');
        $subject2 = (string) ($personalInfo['teaching_subjects_2'] ?? '');

        return self::subjectMatches($subject1) || self::subjectMatches($subject2);
    }

    /**
     * Returns true if the raw programme/course string matches the approved course.
     */
    public static function courseMatches(string $raw): bool
    {
        $normalised = strtolower(trim($raw));
        if ($normalised === '') {
            return false;
        }

        foreach (self::COURSE_KEYWORDS as $keyword) {
            if (str_contains($normalised, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if the academic_programme in personal_info matches.
     */
    public static function hasApprovedCourse(array $personalInfo): bool
    {
        $programme = (string) ($personalInfo['academic_programme'] ?? '');
        return self::courseMatches($programme);
    }

    /**
     * Returns true if the NIN indicates Female (prefix "CF").
     */
    public static function isFemale(array $personalInfo): bool
    {
        $nin    = trim((string) ($personalInfo['nin'] ?? ''));
        $prefix = strtoupper(substr($nin, 0, 2));

        return $prefix === self::FEMALE_NIN_PREFIX;
    }

    /**
     * Returns true only when ALL three eligibility criteria are met.
     */
    public static function isEligible(array $personalInfo): bool
    {
        return self::isFemale($personalInfo)
            && self::hasApprovedCourse($personalInfo)
            && self::hasApprovedSubject($personalInfo);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Introspection helpers (used by the observation / cleanup view)
    // ─────────────────────────────────────────────────────────────────────────

    /** Human-readable list of approved subjects (for display in the UI). */
    public static function approvedSubjectLabels(): array
    {
        return [
            'Biology',
            'Chemistry',
            'Physics',
            'Mathematics',
            'Agriculture',
            'Computer Studies / ICT / Information Technology',
        ];
    }

    /** Human-readable list of approved courses (for display in the UI). */
    public static function approvedCourseLabels(): array
    {
        return [
            'Bachelor of Science with Education',
        ];
    }

    /** Human-readable approved gender (for display in the UI). */
    public static function approvedGenderLabel(): string
    {
        return 'Female (NIN prefix CF)';
    }
}
