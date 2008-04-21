
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



-- wtf

  SELECT  INDUSTRY,
          NEW_APPLICATIONS,
          TOTAL_POSITIONS - FILLED_POSITIONS AS AVAILABLE_POSITIONS,
          FILLED_POSITIONS
    FROM (
          SELECT  J.INDUSTRY,
                  COUNT(*) AS NEW_APPLICATIONS
            FROM  APPLICATION A,
                  JOB J
           WHERE  A.JOB_ID = J.JOB_ID
             AND  MONTH(A.OPEN_DATE) = MONTH('%s')
        GROUP BY  J.INDUSTRY
    ) NEW_APPLICATIONS_TABLE NATURAL JOIN (
          SELECT  J.INDUSTRY,
                  SUM(J.NUM_POSITIONS) AS TOTAL_POSITIONS
            FROM  JOB J
           WHERE  MONTH(J.POST_DATE) <= MONTH('%s')
        GROUP BY  J.INDUSTRY
    ) TOTAL_POSITIONS_TABLE NATURAL JOIN (
          SELECT  J.INDUSTRY,
                  COUNT(*) AS FILLED_POSITIONS
            FROM  JOB J,
                  APPLICATION A
           WHERE  A.JOB_ID = J.JOB_ID
             AND  MONTH(A.CLOSE_DATE) <= MONTH('%s')
             AND  A.STATUS = '4'
        GROUP BY  J.INDUSTRY
    ) FILLED_POSITIONS_TABLE






  SELECT  NEW_APPLICATIONS,
          TOTAL_POSITIONS - FILLED_POSITIONS AS AVAILABLE_POSITIONS,
          FILLED_POSITIONS
    FROM (
          SELECT  COUNT(*) AS NEW_APPLICATIONS
            FROM  APPLICATION A,
                  JOB J
           WHERE  A.JOB_ID = J.JOB_ID
             AND  MONTH(A.OPEN_DATE) = MONTH('2008-04-01')
    ) NEW_APPLICATIONS_TABLE NATURAL JOIN (
          SELECT  SUM(J.NUM_POSITIONS) AS TOTAL_POSITIONS
            FROM  JOB J
           WHERE  MONTH(J.POST_DATE) <= MONTH('2008-04-01')
    ) TOTAL_POSITIONS_TABLE NATURAL JOIN (
          SELECT  COUNT(*) AS FILLED_POSITIONS
            FROM  JOB J,
                  APPLICATION A
           WHERE  A.JOB_ID = J.JOB_ID
             AND  MONTH(A.CLOSE_DATE) <= MONTH('2008-04-01')
             AND  A.STATUS = '4'
    ) FILLED_POSITIONS_TABLE



          SELECT  J.INDUSTRY,
                  COUNT(*) AS NEW_APPLICATIONS
            FROM  APPLICATION A,
                  JOB J
           WHERE  A.JOB_ID = J.JOB_ID
        GROUP BY  J.INDUSTRY
