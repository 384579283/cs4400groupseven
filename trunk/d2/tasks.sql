-- Applicant login
  SELECT  COUNT(*) AS SUCCESS
    FROM  CUSTOMER C,
          APPLICANT A
   WHERE  C.USER_ID = A.USER_ID
     AND  C.EMAIL = '<email>'
     AND  C.PASSWORD = '<password>';

-- Create customer account
  INSERT  INTO  CUSTOMER (USER_ID, PASSWORD, EMAIL, NAME)
          VALUES  ('<id>', '<password>', '<email>', '<name>');

-- Add applicant profile
  INSERT  INTO APPLICANT (USER_ID, PHONE, HIGHEST_DEGREE, YEARS_EXPERIENCE,
                          CITIZENSHIP, BIRTH_YEAR, DESCRIPTION)
          VALUES  ('<id>', '<phone>', '<degree>', '<yearsExperience>',
                   '<citizenship>', '<birthYear>', '<description>');

-- Edit applicant profile
  UPDATE  APPLICANT
     SET  PHONE = '<phone>',
          HIGHEST_DEGREE = '<highestDegree>',
          YEARS_EXPERIENCE = '<yearsExperience>',
          CITIZENSHIP = '<citizenship>',
          BIRTH_YEAR = '<birthYear>',
          DESCRIPTION = '<description>'
   WHERE  USER_ID = '<applicantId>';

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

-- Recruiter login
  SELECT  COUNT(*) AS SUCCESS
    FROM  CUSTOMER C,
          RECRUITER R
   WHERE  C.USER_ID = R.USER_ID
     AND  C.EMAIL = '<email>'
     AND  C.PASSWORD = '<password>';

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

-- Show jobs status
  SELECT  J.JOB_ID,
          J.TITLE,
          --# waiting for tests
          (  SELECT COUNT(*)
              FROM  APPLICATION 
             WHERE  A.JOB_ID = J.JOB_ID
                    AND  A.STATUS = (  SELECT DISTINCT ID
                                        FROM  APPLICATION_STATUS_LU
                                       WHERE  UPPER(NAME) LIKE '%TEST%')) AS WAITING_FOR_TESTS,
          -- # waiting for interviews,
          (  SELECT COUNT(*)
              FROM  APPLICATION 
             WHERE  A.JOB_ID = J.JOB_ID
                    AND  A.STATUS = (  SELECT DISTINCT ID
                                        FROM  APPLICATION_STATUS_LU
                                       WHERE  UPPER(NAME) LIKE '%INTERVIEW%')) AS WAITING_FOR_INTERVIEWS,
          --# waiting for decisions,
          (  SELECT COUNT(*)
              FROM  APPLICATION 
             WHERE  A.JOB_ID = J.JOB_ID
                    AND  A.STATUS = (  SELECT DISTINCT ID
                                        FROM  APPLICATION_STATUS_LU
                                       WHERE  UPPER(NAME) LIKE '%DECISION%')) AS WAITING_FOR_DECISIONS,
          --# positions filled
          (  SELECT COUNT(*)
              FROM  APPLICATION 
             WHERE  A.JOB_ID = J.JOB_ID
                    AND  A.STATUS = (  SELECT DISTINCT ID
                                        FROM  APPLICATION_STATUS_LU
                                       WHERE  UPPER(NAME) LIKE '%ACCEPTED%')) AS POSITIONS_FILLED,
          J.NUM_POSITIONS,
          J.POST_DATE
    FROM  JOB J;
-- Show applications status
  SELECT  A.APPLICATION_ID,
          --name
          (  SELECT NAME
              FROM  APPLICANT B
             WHERE  B.APPLICANT_ID = A.APPLICANT_ID) AS NAME,
          --status
          (  SELECT NAME
              FROM  APPLICATION_STATUS_LU L
             WHERE  L.ID = (  SELECT STATUS
                               FROM  APPLICANT C
                              WHERE  C.APPLICANT_ID = A.APPLICANT_ID)) AS STATUS,
          TEST_SCORE
    FROM  APPLICATION A;
-- Update score
  UPDATE  APPLICATION
     SET  TEST_SCORE='<score>'
   WHERE  APPLICANT_ID='<applicantId>';
-- Post new job
  INSERT  INTO JOB (JOB_ID, POSTED_BY, POST_DATE,
                    TITLE, DESCRIPTION, POSITION_TYPE,
                    INDUSTRY, MINIMUM_SALARY, TEST_TYPE,
                    MIN_TEST_SCORE, EMAIL, PHONE, FAX, 
                    NUM_POSITIONS)
          VALUES ('<jobId>', '<postedBy>', '<postDate>',
                  '<title>', '<description>', '<positionType>',
                  '<industry>', '<minimumSalary>', '<testType>',
                  '<minTestScore>', '<email>', '<phone>', '<fax>',
                  '<numPositions>');
-- Search for applicants
-- Show applicant's detail
-- Admin login
-- Show report by industry
-- Show report by salary
