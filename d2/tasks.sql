-- Applicant login
  SELECT  COUNT(*) AS SUCCESS
    FROM  CAREER_USER U,
          CUSTOMER C,
          APPLICANT A
   WHERE  U.USER_ID = A.USER_ID
     AND  C.EMAIL = '<email>'
     AND  U.PASSWORD = '<password>';

-- Create applicant account
  INSERT  INTO  CUSTOMER (USER_ID, PASSWORd, EMAIL, NAME,
                DESCRIPTION, PHONE)
          VALUES  ('<id>', '<password>', '<email>', '<name>',
                   '<description>', '<phone>');

-- Add applicant profile
  INSERT  INTO APPLICANT (USER_ID, HIGHEST_DEGREE, YEARS_EXPERIENCE,
                          CITIZENSHIP, BIRTH_YEAR)
          VALUES  ('<id>', '<degree>', '<years_experience>',
                   '<citizenship>', '<birth_year>');

-- Search for jobs
-- Show job details
-- Apply
-- Show company details
-- Show applications' status for all jobs
-- Show applications' statuc for jobs in process
-- Applicant profile update
-- Recruiter login
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

