-- Applicant login
  SELECT  COUNT(*) AS SUCCESS
    FROM  CUSTOMER C,
          APPLICANT A
   WHERE  C.USER_ID = A.USER_ID
     AND  C.EMAIL = '<email>'
     AND  C.PASSWORD = '<password>';

-- Create applicant account
  INSERT  INTO  CUSTOMER (USER_ID, PASSWORD, EMAIL, NAME)
          VALUES  ('<id>', '<password>', '<email>', '<name>');

-- Add applicant profile
  INSERT  INTO APPLICANT (USER_ID, PHONE, HIGHEST_DEGREE, YEARS_EXPERIENCE,
                          CITIZENSHIP, BIRTH_YEAR, DESCRIPTION)
          VALUES  ('<id>', '<phone>', '<degree>', '<yearsExperience>',
                   '<citizenship>', '<birthYear>', '<description>');

-- Search for jobs
  SELECT  J.TITLE,
          R.COMPANY_NAME AS EMPLOYER,
          J.POSITION_TYPE,
          J.INDUSTRY,
          J.MINIMUM_SALARY
    FROM  JOB J,
          RECRUITER R
   WHERE  J.POSTED_BY = R.USER_ID
     AND  J.POSITION_TYPE = '<positionType>'
     AND  J.INDUSTRY = '<industry>',
     AND  J.TITLE LIKE '%<keyword_1>%<keyword_2>%',
     AND  J.MINIMUM_SALARY >= '<minimumSalary>';

-- Show job details
  SELECT  J.TITLE,
          J.NUM_POSITIONS,
          J.INDUSTRY,
          J.POSITION_TYPE,
          J.MINIMUM_SALARY,
          J.TEST_TYPE,
          J.MIN_TEST_SCORE,
          J.EMAIL,
          J.FAX,
          J.DESCRIPTION
    FROM  JOB J
   WHERE  J.JOB_ID = '<jobId>';

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

-- Applicant profile update
  UPDATE  APPLICANT
     SET  PHONE = '<phone>',
          HIGHEST_DEGREE = '<highestDegree>',
          YEARS_EXPERIENCE = '<yearsExperience>',
          CITIZENSHIP = '<citizenship>',
          BIRTH_YEAR = '<birthYear>',
          DESCRIPTION = '<description>'
   WHERE  USER_ID = '<applicantId>';

-- Recruiter login
  SELECT  COUNT(*) AS SUCCESS
    FROM  CUSTOMER C,
          RECRUITER R
   WHERE  C.USER_ID = R.USER_ID
     AND  C.EMAIL = '<email>'
     AND  C.PASSWORD = '<password>';

-- Create recruiter account



-- Show jobs status
-- Show applications status
-- Update scores
-- Post new job
-- Search for applicants
-- Show applicant's detail
-- Recruiter profile update
-- Admin login
-- Show report by industry
-- Show report by salary

