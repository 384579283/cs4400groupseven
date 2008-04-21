
-- Search for applicants
  SELECT  NAME,
          HIGHEST_DEGREE,
          BIRTH_YEAR,
          YEARS_EXPERIENCE,
          CITIZENSHIP
    FROM  APPLICANT
   WHERE  HIGHEST_DEGREE>='<highestDegree>'
          AND BIRTH_YEAR BETWEEN DATE('<lowerYear>') AND DATE('<upperYear>')
          AND YEARS_EXPERIENCE >= '<yearsExperience>'
          AND ('<citizenship>' IS NULL OR CITIZENSHIP='<citizenship>');


