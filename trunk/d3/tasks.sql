
-- Show applications status
  SELECT  A.APPLICATION_ID,
          --name
          (  SELECT NAME
              FROM  APPLICANT B
             WHERE  B.USER_ID = A.APPLICANT_ID) AS NAME,
          --status
          (  SELECT NAME
              FROM  APPLICATION_STATUS_LU L
             WHERE  L.ID = (  SELECT STATUS
                               FROM  APPLICANT C
                              WHERE  C.USER_ID = A.APPLICANT_ID)) AS STATUS,
          TEST_SCORE
    FROM  APPLICATION A;

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

-- Show applicant's detail
  SELECT  NAME,
          EMAIL,
          PHONE,
          HIGHEST_DEGREE,
          YEARS_EXPERIENCE,
          CITIZENSHIP,
          BIRTH_YEAR,
          DESCRIPTION
   FROM  APPLICANT NATURAL JOIN CUSTOMER; 

