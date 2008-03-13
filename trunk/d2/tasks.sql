-- Applicant login
  SELECT  COUNT(*) AS SUCCESS
    FROM  USER U, APPLICANT A
   WHERE  U.USER_ID = A.USER_ID
     AND  U.PASSWORD = '<password>';

-- Create applicant account
-- Add applicant profile
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

