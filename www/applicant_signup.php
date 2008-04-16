<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <link rel="stylesheet" href="style.css" type="text/css"/>
    <style>
      td {
        padding: .4em;
        text-align: left;
        vertical-align: top;
      }
      table {
        margin: 2em auto 0 auto;
      }
      .profileHeader {
        font-weight: bold;
        padding-top: 2em;
      }
      .submitCell {
        text-align: center;
        padding-top: 2em;
      }
    </style>
  </head>
  <body>
    <h1>
      Create an Applicant Account
    </h1>
    <table>
      <tr>
        <td>
          * Name
        </td>
        <td>
          <input type="text" />
        </td>
      </tr>
      <tr>
        <td>
          * Email
        </td>
        <td>
          <input type="text" />
        </td>
      </tr>
      <tr>
        <td>
          * Choose a password
        </td>
        <td>
          <input type="password" />
        </td>
      </tr>
      <tr>
        <td>
          * Re-enter password
        </td>
        <td>
          <input type="password" />
        </td>
      </tr>
      <tr>
        <td colspan="2" class="profileHeader">
          Your Profile (You can update it later)
        </td>
      </tr>
      <tr>
        <td>
          Phone
        </td>
        <td>
          <input type="text" />
        </td>
      </tr>
      <tr>
        <td>
          Highest degree
        </td>
        <td>
          <select>
          </select>
        </td>
      </tr>
      <tr>
        <td>
          Birth year
        </td>
        <td>
          <select>
          <? for ($i = date("Y") - 123; $i <= date("Y"); $i++) { ?>
            <option value="<? echo $i ?>"<?
                if ($i == date("Y")) { echo ' selected="selected"'; }
              ?>>
              <? echo $i ?>
            </option>
          <? } ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>
          Years of experience
        </td>
        <td>
          <select>
          </select>
        </td>
      </tr>
      <tr>
        <td>
          Citizenship
        </td>
        <td>
          <select>
          </select>
        </td>
      </tr>
      <tr>
        <td>
          Short description
          <br />
          (Not to exceed 500 characters)
        </td>
        <td>
          <textarea></textarea>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="submitCell">
          <input type="Submit" value="Submit" />
        </td>
      </tr>
    </table>
  </body>
</html>

