
-- Apply
  INSERT  INTO APPLICATION (APPLICATION_ID, APPLICANT_ID, JOB_ID,
                            STATUS, OPEN_DATE)
          VALUES ('<applicationId>', '<applicantId>', '<jobId>',
                  '1', '<openDate>');

-- Show company details
  SELECT  R.COMPANY_NAME,
          C.EMAIL,
          C.PHONE,
          R.FAX,
          R.WEBSITE,
          R.DESCRIPTION
    FROM  CUSTOMER C,
          RECRUITER R
   WHERE  C.USER_ID = R.USER_ID
     AND  R.USER_ID = '<recruiterId>';

-- Show applications' status for all jobs
  SELECT  J.TITLE,
          R.COMPANY_NAME,
          A.OPEN_DATE,
          A.STATUS
    FROM  JOB J,
          RECRUITER R,
          APPLICATION A
   WHERE  J.POSTED_BY = R.USER_ID
     AND  A.JOB_ID = J.JOB_ID
     AND  A.APPLICANT_ID = '<applicantId>';

-- Show applications' status for jobs in process
  SELECT  J.TITLE,
          R.COMPANY_NAME,
          A.OPEN_DATE,
          A.STATUS
    FROM  JOB J,
          RECRUITER R,
          APPLICATION A
   WHERE  J.POSTED_BY = R.USER_ID
     AND  A.JOB_ID = J.JOB_ID
     AND  A.APPLICANT_ID = '<applicantId>'
     AND  A.CLOSE_DATE = NULL;

-- Create recruiter account
  INSERT  INTO  CUSTOMER (USER_ID, PASSWORD, EMAIL, NAME)
          VALUES  ('<id>', '<password>', '<email>', '<name>');
  INSERT  INTO RECRUITER (USER_ID, COMPANY_NAME, PHONE, FAX,
                          WEBSITE, DESCRIPTION)
          VALUES  ('<id>', '<companyName>', '<phone>', '<fax>',
                   '<website>', '<description>');

-- Recruiter profile update (phone, fax, website, description)
  UPDATE  RECRUITER
     SET  PHONE = '<phone>',
          FAX = '<fax>',
          WEBSITE = '<website>',
          DESCRIPTION = '<description>'
   WHERE  USER_ID = '<applicantId>';

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

-- Update score
  UPDATE  APPLICATION
     SET  TEST_SCORE='<score>'
   WHERE  APPLICANT_ID='<applicantId>';

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

-- Admin login
  SELECT  COUNT(*) AS SUCCESS
    FROM  ADMIN
   WHERE  USER_ID = '<userId>'
          AND PASSWORD = '<password>';

