
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

