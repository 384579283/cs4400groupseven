<? require_once('db.php'); ?>
<table class="header">
  <tr>
    <td>
      Hello, <? echo $db->get_customer_name($_SESSION['user_id']); ?>.
    </td>
    <td>
      <a href="sign_out.php">Sign out</a>
    </td>
    <td>
      <a href="job_search.php">Jobs</a>
    </td>
    <td>
      <a href="applicant_status.php">Status</a>
    </td>
    <td>
      <a href="applicant_profile.php">Profile</a>
    </td>
  </tr>
</table>

