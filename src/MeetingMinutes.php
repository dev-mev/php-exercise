<?php

namespace App;

class MeetingMinutes
{
    /**
     * Provides sum of weekly meeting minutes for all meetings of a Course Section.
     *
     * The $section argument will be one Course Section record response from a UW Web Service.
     * Expect this to be a PHP stdClass instance returned by json_decode().
     *
     * See the file data/uw-course-section-service-mock.json for example data returned from
     * this web service. This contains an array of Course Section records. Each Course
     * Section is a JSON object starting with properties "Term", "CourseCampus", "CreditControl".
     *
     * @param \stdClass $section
     * @return array
     */
    public function countWeeklyMeetingMinutes($section)
    {
        $out = 0;
        foreach ($section->Meetings as $meeting) {
            $out += $this->countOneMeeting($meeting);
        }
        return $out;
    }

    /**
     * Count the weekly meeting minutes for one Meeting record.
     *
     * Each UW course meeting can occur on multiple days, but always with the same start and
     * end time. (Different times are represented as a different Meeting record.) Weekly minutes
     * will be the difference between the start and end time times the number of days.
     *
     * If the meeting has no days, no start time, or no end time return 0.
     *
     * @param \stdClass $section
     * @return array
     */
    private function countOneMeeting($meeting): int
    {
        if (!empty($meeting->DaysOfWeek->Days) && !empty($meeting->StartTime) && !empty($meeting->EndTime)) {
            $minutes = 0;

            foreach ($meeting->DaysOfWeek->Days as $day) {
                $start_time = explode(':', $meeting->StartTime);
                $end_time = explode(':', $meeting->EndTime);

                // multiply by 60 to change hours to minutes
                $minutes += ($end_time[0] * 60 + $end_time[1]) - ($start_time[0] * 60 + $start_time[1]);
            }

            return $minutes;
        }

        return 0;
    }
}
