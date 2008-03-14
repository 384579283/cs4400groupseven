-- Create position type lookup table
CREATE TABLE POSITION_TYPE_LU (
  ID    INT          NOT NULL,
  NAME  VARCHAR(32)  NOT NULL,
  PRIMARY KEY(ID)
);

-- Populate position type lookup table
INSERT INTO POSITION_TYPE_LU (ID, NAME)
  VALUES (1, 'Full time'),
         (2, 'Part time'),
         (3, 'Internship'),
         (4, 'Temporary');

-- Create industry lookup table
CREATE TABLE INDUSTRY_LU (
  ID    INT          NOT NULL,
  NAME  VARCHAR(32)  NOT NULL,
  PRIMARY KEY(ID)
);

-- Populate industry lookup table
INSERT INTO INDUSTRY_LU (ID, NAME)
  VALUES (1, 'Accounting'),
         (2, 'Computers'),
         (3, 'Education'),
         (4, 'Fashion'),
         (5, 'Insurance');

-- Create degree lookup table
CREATE TABLE DEGREE_LU (
  ID    INT          NOT NULL,
  NAME  VARCHAR(32)  NOT NULL,
  PRIMARY KEY(ID)
);

-- Populate degree lookup table
INSERT INTO DEGREE_LU (ID, NAME)
  VALUES (1, 'High School or below'),
         (2, 'Bachelor'),
         (3, 'Master'),
         (4, 'PhD');

-- Create application status lookup table
CREATE TABLE APPLICATION_STATUS_LU (
  ID    INT          NOT NULL,
  NAME  VARCHAR(32)  NOT NULL,
  PRIMARY KEY(ID)
);

-- Populate application status lookup table
INSERT INTO APPLICATION_STATUS_LU (ID, NAME)
  VALUES (1, 'In test process'),
         (2, 'In interview process'),
         (3, 'In decision process'),
         (4, 'Declined')
         (5, 'Accepted');

-- Create test type lookup table
CREATE TABLE TEST_TYPE_LU (
  ID    INT          NOT NULL,
  NAME  VARCHAR(32)  NOT NULL,
  PRIMARY KEY(ID)
);

-- Populate test type lookup table
INSERT INTO TEST_TYPE_LU (ID, NAME)
  VALUES (1, 'GRE'),
         (2, 'GMAT'),
         (3, 'MCAT'),
         (4, 'STAR'),
         (5, 'CERT');

-- Create administrator table
CREATE TABLE ADMINISTRATOR (
  ADMIN_ID  INT          NOT NULL,
  PASSWORD  VARCHAR(32)  NOT NULL,
  PRIMARY KEY(ADMIN_ID)
);

-- Create customer table
CREATE TABLE CUSTOMER (
  USER_ID      INT           NOT NULL,
  PASSWORD     VARCHAR(32)   NOT NULL,
  EMAIL        VARCHAR(220)  NOT NULL,
  NAME         VARCHAR(255)  NOT NULL,
  PRIMARY KEY(USER_ID),
  FOREIGN KEY(USER_ID)      REFERENCES CAREER_USER(USER_ID)
                            ON DELETE CASCADE ON UPDATE CASCADE
);

-- Create recruiter table
CREATE TABLE RECRUITER (
  USER_ID       INT           NOT NULL,
  COMPANY_NAME  VARCHAR(255)  NOT NULL,
  PHONE         CHAR(10),
  FAX           CHAR(10),
  WEBSITE       VARCHAR(255),
  DESCRIPTION   VARCHAR(500),
  PRIMARY KEY(USER_ID),
  FOREIGN KEY(USER_ID)      REFERENCES CAREER_USER(USER_ID)
                            ON DELETE CASCADE ON UPATE CASCADE
);

-- Create applicant table
CREATE TABLE APPLICANT (
  USER_ID           INT           NOT NULL,
  PHONE             CHAR(10),
  HIGHEST_DEGREE    INT,
  YEARS_EXPERIENCE  INT,
  CITIZENSHIP       VARCHAR(255),
  BIRTH_YEAR        INT,
  DESCRIPTION       VARCHAR(500),
  PRIMARY KEY(USER_ID),
  FOREIGN KEY(USER_ID)        REFERENCES CAREER_USER(USER_ID)
                              ON DELETE CASCADE ON UPATE CASCADE,
  FOREIGN KEY(HIGHEST_DEGREE) REFERENCES DEGREE(ID)
);

-- Create application table
CREATE TABLE APPLICATION (
  APPLICATION_ID  INT      NOT NULL,
  APPLICANT_ID    INT      NOT NULL,
  JOB_ID          INT      NOT NULL,
  TEST_SCORE      INT,
  STATUS          CHAR(1)  NOT NULL,
  OPEN_DATE       DATE     NOT NULL,
  CLOSE_DATE      DATE,
  PRIMARY KEY(APPLICATION_ID),
  FOREIGN KEY(APPLICANT_ID) REFERENCES APPLICANT(USER_ID)
                            ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(JOB_ID)       REFERENCES JOB(JOB_ID)
                            ON DELETE CASCADE ON UPDATE CASCADE
);

-- Create job table
CREATE TABLE JOB (
  JOB_ID           INT           NOT NULL,
  POSTED_BY        INT           NOT NULL,
  POST_DATE        DATE          NOT NULL,
  TITLE            VARCHAR(255)  NOT NULL,
  DESCRIPTION      VARCHAR(500),
  POSITION_TYPE    INT           NOT NULL,     
  INDUSTRY         INT,
  MINIMUM_SALARY   INT           NOT NULL,
  TEST_TYPE        INT,
  MIN_TEST_SCORE   INT,
  EMAIL            VARCHAR(220)  NOT NULL,
  PHONE            CHAR(10)      NOT NULL,
  FAX              CHAR(10),
  NUM_POSITIONS    INT,
  PRIMARY KEY(JOB_ID),
  FOREIGN KEY(POSITION_TYPE) REFERENCES POSITION_TYPE_LU(ID),
  FOREIGN KEY(INDUSTRY)      REFERENCES INDUSTRY_LU(ID),
  FOREIGN KEY(TEST_TYPE)     REFERENCES TEST_TYPE(ID),
  FOREIGN KEY(USER_ID)       REFERENCES RECRUITER(USER_ID)
                             ON DELETE CASCADE ON UPDATE CASCADE
);

