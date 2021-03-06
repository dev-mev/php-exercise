<?php

namespace App;

class CourseSectionArea
{
    private $facultyArea;

    public function __construct(FacultyArea $facultyArea)
    {
        $this->facultyArea = $facultyArea;
    }

    /**
     * Provides a list of college areas related to a course section using instructors.
     *
     * Return an array containing a list of all related areas sorted alphabetically. The
     * returned list must not contain the same are more than once.
     *
     * This method will receive one Course Section record response from a UW Web Service.
     * Expect this to be a PHP stdClass instance returned by json_decode().
     *
     * See the file data/uw-course-section-service-mock.json for example data returned from
     * this web service. This contains an array of Course Section records. Each Course
     * Section is a JSON object starting with properties "Term", "CourseCampus", "CreditControl".
     *
     * @param \stdClass $section
     * @return array
     */
    public function areasFor(\stdClass $section): array
    {
        $out = [];

        foreach ($section->Meetings as $key => $value) {
            foreach ($value->Instructors as $key => $value) {
                $area = $this->facultyArea->areaOf($value->Person->RegID);

                if ($area !== NULL && !in_array($area, $out)) {
                    array_push($out, $area);
                }
            }
        }

        sort($out);

        return $out;
    }
}
