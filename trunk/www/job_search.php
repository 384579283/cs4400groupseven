<?

$checked_position_types = array();

if ($_GET['searching']) {

    foreach ($db->lookup_position_type() as $id => $name) {
        if ($_GET['position_type_' + $id]) {
            $checked_position_types[] = $id;
        }
    }

    register_optional_get_keys(
        'industry',
        'title_keywords',
        'minimum_salary');

}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <link rel="stylesheet" href="style.css" type="text/css"/>
  </head>
  <body><form action="job_search.php" method="GET">
    <input type="hidden" name="searching" value="true"/>
    <? $tab = 'job'; include('applicant_header.php'); ?>
    <h1>Job Search</h1>
    <? include('error.php'); ?>
    <table class="box">
      <tr>
        <td>
          Position type
        </td>
        <td>
        <? foreach ($db->lookup_position_type() as $id => $name) { ?>
          <input type="checkbox" name="position_type_<? echo $id; ?>"/>
          <? echo $name; ?><br/>
        <? } ?>
        </td>
      </tr>
      <tr>
        <td>
          Industry
        </td>
        <td>
          <select name="industry">
          <? foreach ($db->lookup_industry() as $id => $name) { ?>
            <option value="<? echo "$id"; ?>">
              <? echo $name; ?>
            </option>
          <? } ?>
            <option value="" selected="selected"></option>
          </select>
        </td>
      </tr>
      <tr>
        <td>
          Keywords in job title
        </td>
        <td>
          <input type="text" name="title_keywords" />
        </td>
      </tr>
      <tr>
        <td>
          Minimum salary
        </td>
        <td>
          <input type="text" name="minimum_salary" />
        </td>
      </tr>
      <tr>
        <td colspan="2" class="submitCell">
          <input type="submit" value="Search" class="btn" />
        </td>
      </tr>
    </table>
  </form></body>
</html>

