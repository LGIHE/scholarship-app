<?php

namespace App\Services;

use App\Models\Application;

class ScoringService
{
    /**
     * Calculate and save the score for an application
     * 
     * Weights:
     * - Financial Need (30 pts)
     * - Academic Merit (25 pts)
     * - Demographics (15 pts)
     * - Commitment (15 pts)
     * - Essay Quality (15 pts)
     * Total: 100 pts
     */
    public function score(Application $application): void
    {
        $breakdown = [
            'financial_need' => $this->calculateFinancialNeed($application->financial_info ?? []),
            'academic_merit' => $this->calculateAcademicMerit($application->personal_info ?? []),
            'demographics'   => $this->calculateDemographics($application->personal_info ?? []),
            'commitment'     => $this->calculateCommitment($application->essay ?? []),
            'essay_quality'  => $this->calculateEssayQuality($application->essay ?? []),
        ];

        $totalScore = array_sum($breakdown);

        $breakdown['total'] = $totalScore;

        $application->scoring_breakdown = $breakdown;
        $application->save();
    }

    private function calculateFinancialNeed(array $financialInfo): int
    {
        $score = 0;
        
        // Income component (Up to 20 pts)
        $income = floatval($financialInfo['household_income'] ?? 100000);
        if ($income < 20000) {
            $score += 20;
        } elseif ($income < 40000) {
            $score += 15;
        } elseif ($income < 60000) {
            $score += 10;
        } elseif ($income < 80000) {
            $score += 5;
        } else {
            $score += 0;
        }

        // Dependents component (Up to 10 pts)
        $dependents = intval($financialInfo['number_of_dependents'] ?? 0);
        $score += min(10, $dependents * 2);

        return $score;
    }

    private function calculateAcademicMerit(array $personalInfo): int
    {
        // Max 25 points
        // Check for CGPA first, then GPA, default to 0 if neither exists (for first-year students)
        $gpa = floatval($personalInfo['cgpa'] ?? $personalInfo['gpa'] ?? 0);
        
        // If no GPA provided (first-year students), give base points
        if ($gpa == 0) {
            return 10; // Base points for first-year students without CGPA
        }
        
        // Formula: (GPA / 4.0) * 25
        $score = ($gpa / 4.0) * 25;
        
        return (int) min(25, max(0, $score));
    }

    private function calculateDemographics(array $personalInfo): int
    {
        // Max 15 points
        $score = 5; // Base points
        
        // Example logic: female applicants might get extra points in some STEM scholarships
        if (strtolower($personalInfo['gender'] ?? '') === 'female') {
            $score += 5;
        }

        // Example logic: rural applicants get extra points
        if (($personalInfo['residence_area'] ?? '') === 'rural') {
            $score += 5;
        }

        return (int) min(15, $score);
    }

    private function calculateCommitment(array $essay): int
    {
        // Max 15 points
        // Check commitment essay word count
        $commitmentText = $essay['commitment'] ?? '';
        $wordCount = str_word_count(strip_tags($commitmentText));
        
        // Let's say 100 words gets full 15 points
        $score = ($wordCount / 100) * 15;
        
        return (int) min(15, max(0, $score));
    }

    private function calculateEssayQuality(array $essay): int
    {
        // Max 15 points
        // Check personal statement word count as proxy for quality in dev phase
        $statementText = $essay['personal_statement'] ?? '';
        $wordCount = str_word_count(strip_tags($statementText));
        
        // Let's say 300 words gets full 15 points
        $score = ($wordCount / 300) * 15;
        
        return (int) min(15, max(0, $score));
    }
}
